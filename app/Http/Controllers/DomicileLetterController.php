<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDomicileLetterRequest;
use App\Http\Resources\DomicileLetterResource;
use App\Models\DomicileLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class DomicileLetterController extends Controller
{
    // Default static values for village header
    private const KOP_DEFAULTS = [
        'kabupaten' => 'Tasikmalaya',
        'kecamatan' => 'Cigalontang',
        'nama_desa' => 'Sirnaputra',
        'alamat'    => 'Jl. Raya Sirnaputra No. 01',
        'kode_pos'  => '46463',
    ];

    private const TTD_DEFAULTS = [
        'jabatan_penandatangan' => 'Kepala Desa',
        'nama_penandatangan'    => 'H. AHMAD SUPRIATNA',
    ];

    public function index(Request $request): Response
    {
        $query = DomicileLetter::query();

        if ($search = $request->string('search')->trim()) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                    ->orWhere('nama_warga', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $letters = $query
            ->latest('tanggal_surat')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('DomicileLetter/Index', [
            'letters' => DomicileLetterResource::collection($letters),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('DomicileLetter/Create');
    }

    public function store(StoreDomicileLetterRequest $request)
    {
        $data = $request->validated();

        // Apply static defaults for kop surat fields that aren't filled
        foreach (self::KOP_DEFAULTS as $key => $default) {
            if (empty($data[$key])) {
                $data[$key] = $default;
            }
        }
        foreach (self::TTD_DEFAULTS as $key => $default) {
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

        $paths = [];
        $cleanupPaths = [];

        foreach ($uploads as $column => $file) {
            if ($file) {
                $savedPath = $file->store('surat-keterangan-domisili', 'public');
                $paths[$column . '_path'] = $savedPath;
                $cleanupPaths[] = $savedPath;
            }
        }

        try {
            $letter = DomicileLetter::create([
                ...$data,
                ...$paths,
                'created_by' => $request->user()?->id,
            ]);
        } catch (\Throwable $e) {
            foreach ($cleanupPaths as $path) {
                Storage::disk('public')->delete($path);
            }
            throw $e;
        }

        return redirect()
            ->route('surat-keterangan-domisili.show', $letter)
            ->with('success', "Surat Keterangan Domisili atas nama \"{$letter->nama_warga}\" berhasil dibuat dan siap dicetak.");
    }

    public function show(DomicileLetter $domicileLetter): Response
    {
        return Inertia::render('DomicileLetter/Show', [
            'letter' => DomicileLetterResource::make($domicileLetter->load('creator'))->resolve(),
        ]);
    }

    public function edit(DomicileLetter $domicileLetter): Response
    {
        return Inertia::render('DomicileLetter/Edit', [
            'letter' => DomicileLetterResource::make($domicileLetter)->resolve(),
        ]);
    }

    public function update(StoreDomicileLetterRequest $request, DomicileLetter $domicileLetter)
    {
        $data = $request->validated();

        foreach (self::KOP_DEFAULTS as $key => $default) {
            if (empty($data[$key])) {
                $data[$key] = $domicileLetter->{$key} ?? $default;
            }
        }

        $uploads = [
            'logo_kiri'    => $request->file('logo_kiri'),
            'logo_kanan'   => $request->file('logo_kanan'),
            'tanda_tangan' => $request->file('tanda_tangan'),
            'stempel'      => $request->file('stempel'),
        ];

        $paths = [];

        foreach ($uploads as $column => $file) {
            if ($file) {
                $oldPath = $domicileLetter->{$column . '_path'};
                if ($oldPath) {
                    Storage::disk('public')->delete($oldPath);
                }
                $paths[$column . '_path'] = $file->store('surat-keterangan-domisili', 'public');
            }
        }

        $domicileLetter->update([
            ...$data,
            ...$paths,
        ]);

        return redirect()
            ->route('surat-keterangan-domisili.show', $domicileLetter)
            ->with('success', "Surat Keterangan Domisili atas nama \"{$domicileLetter->nama_warga}\" berhasil diperbarui.");
    }

    public function destroy(DomicileLetter $domicileLetter)
    {
        $files = array_filter([
            $domicileLetter->logo_kiri_path,
            $domicileLetter->logo_kanan_path,
            $domicileLetter->tanda_tangan_path,
            $domicileLetter->stempel_path,
        ]);

        Storage::disk('public')->delete(array_values(array_unique($files)));
        $domicileLetter->delete();

        return back()->with('success', 'Surat Keterangan Domisili berhasil dihapus.');
    }
}
