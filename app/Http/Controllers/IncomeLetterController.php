<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncomeLetterRequest;
use App\Http\Resources\IncomeLetterResource;
use App\Models\IncomeLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class IncomeLetterController extends Controller
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
        $query = IncomeLetter::query();

        if ($search = $request->string('search')->trim()) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                    ->orWhere('nama_warga', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $letters = $query->latest('tanggal_surat')->paginate(10)->withQueryString();

        return Inertia::render('IncomeLetter/Index', [
            'letters' => IncomeLetterResource::collection($letters),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('IncomeLetter/Create');
    }

    public function store(StoreIncomeLetterRequest $request)
    {
        $data = $request->validated();
        // Apply static kop surat defaults
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

        $paths    = [];
        $allPaths = [];

        foreach ($uploads as $col => $file) {
            if ($file) {
                $p = $file->store('surat-keterangan-penghasilan', 'public');
                $paths[$col . '_path'] = $p;
                $allPaths[]            = $p;
            }
        }

        try {
            $letter = IncomeLetter::create([
                ...$data,
                ...$paths,
                'created_by' => $request->user()?->id,
            ]);
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($allPaths);
            throw $e;
        }

        return redirect()
            ->route('surat-keterangan-penghasilan.show', $letter)
            ->with('success', "Surat Keterangan Penghasilan atas nama \"{$letter->nama_warga}\" berhasil dibuat.");
    }

    public function show(IncomeLetter $incomeLetter): Response
    {
        return Inertia::render('IncomeLetter/Show', [
            'letter' => IncomeLetterResource::make($incomeLetter->load('creator'))->resolve(),
        ]);
    }

    public function edit(IncomeLetter $incomeLetter): Response
    {
        return Inertia::render('IncomeLetter/Edit', [
            'letter' => IncomeLetterResource::make($incomeLetter)->resolve(),
        ]);
    }

    public function update(StoreIncomeLetterRequest $request, IncomeLetter $incomeLetter)
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
                $oldPath = $incomeLetter->{$column . '_path'};
                if ($oldPath) {
                    Storage::disk('public')->delete($oldPath);
                }
                $paths[$column . '_path'] = $file->store('surat-keterangan-penghasilan', 'public');
            }
        }

        $incomeLetter->update([
            ...$data,
            ...$paths,
        ]);

        return redirect()
            ->route('surat-keterangan-penghasilan.show', $incomeLetter)
            ->with('success', "Surat Keterangan Penghasilan atas nama \"{$incomeLetter->nama_warga}\" berhasil diperbarui.");
    }

    public function destroy(IncomeLetter $incomeLetter)
    {
        Storage::disk('public')->delete(array_filter([
            $incomeLetter->logo_kiri_path,
            $incomeLetter->logo_kanan_path,
            $incomeLetter->tanda_tangan_path,
            $incomeLetter->stempel_path,
        ]));

        $incomeLetter->delete();

        return back()->with('success', 'Surat Keterangan Penghasilan berhasil dihapus.');
    }
}
