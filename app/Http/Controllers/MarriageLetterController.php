<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMarriageLetterRequest;
use App\Http\Resources\MarriageLetterResource;
use App\Models\MarriageLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class MarriageLetterController extends Controller
{
    private const KOP_DEFAULTS = [
        'kabupaten' => 'Tasikmalaya',
        'kecamatan' => 'Cigalontang',
        'nama_desa' => 'Sirnaputra',
        'alamat'    => 'Jl. Raya Sirnaputra No. 01',
        'kode_pos'  => '46463',
    ];

    public function index(Request $request): Response
    {
        $query = MarriageLetter::query();

        if ($search = $request->string('search')->trim()) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                    ->orWhere('nama_pemohon', 'like', "%{$search}%")
                    ->orWhere('nama_pasangan', 'like', "%{$search}%");
            });
        }

        $letters = $query->latest('tanggal_surat')->paginate(10)->withQueryString();

        return Inertia::render('MarriageLetter/Index', [
            'letters' => MarriageLetterResource::collection($letters),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('MarriageLetter/Create');
    }

    public function store(StoreMarriageLetterRequest $request)
    {
        $data = $request->validated();

        foreach (self::KOP_DEFAULTS as $key => $default) {
            if (empty($data[$key])) {
                $data[$key] = $default;
            }
        }

        $uploads = [
            'logo_kiri'    => $request->file('logo_kiri'),
            'logo_kanan'   => $request->file('logo_kanan'),
            'tanda_tangan' => $request->file('tanda_tangan'),
            'stempel'      => $request->file('stempel'),
        ];

        $paths      = [];
        $allPaths   = [];

        foreach ($uploads as $col => $file) {
            if ($file) {
                $p = $file->store('surat-keterangan-menikah', 'public');
                $paths[$col . '_path'] = $p;
                $allPaths[]            = $p;
            }
        }

        try {
            $letter = MarriageLetter::create([
                ...$data,
                ...$paths,
                'created_by' => $request->user()?->id,
            ]);
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($allPaths);
            throw $e;
        }

        return redirect()
            ->route('surat-keterangan-menikah.show', $letter)
            ->with('success', "Surat Keterangan Menikah atas nama \"{$letter->nama_pemohon}\" berhasil dibuat.");
    }

    public function show(MarriageLetter $marriageLetter): Response
    {
        return Inertia::render('MarriageLetter/Show', [
            'letter' => MarriageLetterResource::make($marriageLetter->load('creator'))->resolve(),
        ]);
    }

    public function edit(MarriageLetter $marriageLetter): Response
    {
        return Inertia::render('MarriageLetter/Edit', [
            'letter' => MarriageLetterResource::make($marriageLetter)->resolve(),
        ]);
    }

    public function update(StoreMarriageLetterRequest $request, MarriageLetter $marriageLetter)
    {
        $data = $request->validated();

        $uploads = [
            'logo_kiri'    => $request->file('logo_kiri'),
            'logo_kanan'   => $request->file('logo_kanan'),
            'tanda_tangan' => $request->file('tanda_tangan'),
            'stempel'      => $request->file('stempel'),
        ];

        $paths = [];
        foreach ($uploads as $column => $file) {
            if ($file) {
                $oldPath = $marriageLetter->{$column . '_path'};
                if ($oldPath) {
                    Storage::disk('public')->delete($oldPath);
                }
                $paths[$column . '_path'] = $file->store('surat-keterangan-menikah', 'public');
            }
        }

        $marriageLetter->update([
            ...$data,
            ...$paths,
        ]);

        return redirect()
            ->route('surat-keterangan-menikah.show', $marriageLetter)
            ->with('success', "Surat Keterangan Menikah atas nama \"{$marriageLetter->nama_pemohon}\" berhasil diperbarui.");
    }

    public function destroy(MarriageLetter $marriageLetter)
    {
        Storage::disk('public')->delete(array_filter([
            $marriageLetter->logo_kiri_path,
            $marriageLetter->logo_kanan_path,
            $marriageLetter->tanda_tangan_path,
            $marriageLetter->stempel_path,
        ]));

        $marriageLetter->delete();

        return back()->with('success', 'Surat Keterangan Menikah berhasil dihapus.');
    }
}
