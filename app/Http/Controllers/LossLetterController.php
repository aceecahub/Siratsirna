<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLossLetterRequest;
use App\Http\Resources\LossLetterResource;
use App\Models\LossLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class LossLetterController extends Controller
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
        $query = LossLetter::query();

        if ($search = $request->string('search')->trim()) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                    ->orWhere('nama_warga', 'like', "%{$search}%")
                    ->orWhere('barang_hilang', 'like', "%{$search}%");
            });
        }

        $letters = $query->latest('tanggal_surat')->paginate(10)->withQueryString();

        return Inertia::render('LossLetter/Index', [
            'letters' => LossLetterResource::collection($letters),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('LossLetter/Create');
    }

    public function store(StoreLossLetterRequest $request)
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
                $p = $file->store('surat-keterangan-kehilangan', 'public');
                $paths[$col . '_path'] = $p;
                $allPaths[]            = $p;
            }
        }

        try {
            $letter = LossLetter::create([
                ...$data,
                ...$paths,
                'created_by' => $request->user()?->id,
            ]);
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($allPaths);
            throw $e;
        }

        return redirect()
            ->route('surat-keterangan-kehilangan.show', $letter)
            ->with('success', "Surat Keterangan Kehilangan atas nama \"{$letter->nama_warga}\" berhasil dibuat.");
    }

    public function show(LossLetter $lossLetter): Response
    {
        return Inertia::render('LossLetter/Show', [
            'letter' => LossLetterResource::make($lossLetter->load('creator'))->resolve(),
        ]);
    }

    public function edit(LossLetter $lossLetter): Response
    {
        return Inertia::render('LossLetter/Edit', [
            'letter' => LossLetterResource::make($lossLetter)->resolve(),
        ]);
    }

    public function update(StoreLossLetterRequest $request, LossLetter $lossLetter)
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
                $oldPath = $lossLetter->{$column . '_path'};
                if ($oldPath) {
                    Storage::disk('public')->delete($oldPath);
                }
                $paths[$column . '_path'] = $file->store('surat-keterangan-kehilangan', 'public');
            }
        }

        $lossLetter->update([
            ...$data,
            ...$paths,
        ]);

        return redirect()
            ->route('surat-keterangan-kehilangan.show', $lossLetter)
            ->with('success', "Surat Keterangan Kehilangan atas nama \"{$lossLetter->nama_warga}\" berhasil diperbarui.");
    }

    public function destroy(LossLetter $lossLetter)
    {
        Storage::disk('public')->delete(array_filter([
            $lossLetter->logo_kiri_path,
            $lossLetter->logo_kanan_path,
            $lossLetter->tanda_tangan_path,
            $lossLetter->stempel_path,
        ]));

        $lossLetter->delete();

        return back()->with('success', 'Surat Keterangan Kehilangan berhasil dihapus.');
    }
}
