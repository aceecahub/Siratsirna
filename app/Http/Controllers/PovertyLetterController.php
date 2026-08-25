<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePovertyLetterRequest;
use App\Http\Resources\PovertyLetterResource;
use App\Models\PovertyLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PovertyLetterController extends Controller
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
        $query = PovertyLetter::query();

        if ($search = $request->string('search')->trim()) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                    ->orWhere('nama_warga', 'like', "%{$search}%");
            });
        }

        $letters = $query->latest('tanggal_surat')->paginate(10)->withQueryString();

        return Inertia::render('PovertyLetter/Index', [
            'letters' => PovertyLetterResource::collection($letters),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('PovertyLetter/Create');
    }

    public function store(StorePovertyLetterRequest $request)
    {
        $data = $request->validated();

        // Apply static kop defaults
        foreach (self::KOP_DEFAULTS as $key => $default) {
            if (empty($data[$key])) {
                $data[$key] = $default;
            }
        }

        // ── Upload kop & watermark ────────────────────────────────
        $topUploads = [
            'logo_kiri'  => $request->file('logo_kiri'),
            'logo_kanan' => $request->file('logo_kanan'),
            'stempel'    => $request->file('stempel'),
        ];
        $paths = [];
        foreach ($topUploads as $column => $file) {
            if ($file) {
                $paths[$column . '_path'] = $file->store('surat-tidak-mampu', 'public');
            }
        }

        // ── Handle tanda tangan (single signer) ─────────────────
        if ($request->hasFile('tanda_tangan')) {
            $paths['tanda_tangan_path'] = $request->file('tanda_tangan')->store('surat-tidak-mampu', 'public');
        }

        // ── Handle penandatangan_list (JSON string dari form) ────
        $signers = null;
        $signerJson = $request->input('penandatangan_list');
        if ($signerJson) {
            $signers = is_string($signerJson)
                ? json_decode($signerJson, true)
                : (is_array($signerJson) ? $signerJson : null);

            if (is_array($signers)) {
                $allPaths = array_values(array_filter(array_values($paths)));
                foreach ($signers as $idx => &$signer) {
                    $ttdKey = "tanda_tangan_signer_{$idx}";
                    if ($request->hasFile($ttdKey)) {
                        $p = $request->file($ttdKey)->store('surat-tidak-mampu', 'public');
                        $signer['tanda_tangan_path'] = $p;
                        $allPaths[] = $p;
                    }
                    $stempelKey = "stempel_signer_{$idx}";
                    if ($request->hasFile($stempelKey)) {
                        $p = $request->file($stempelKey)->store('surat-tidak-mampu', 'public');
                        $signer['stempel_path'] = $p;
                        $allPaths[] = $p;
                    }
                    unset($signer['tanda_tangan'], $signer['stempel']);
                }
                unset($signer);
            }
        }

        $allPaths = array_values(array_filter(array_values($paths)));

        try {
            $letter = PovertyLetter::create(array_merge(
                $data,
                $paths,
                ['penandatangan_list' => $signers],
                ['created_by' => $request->user()?->id],
            ));
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($allPaths);
            throw $e;
        }

        return redirect()
            ->route('surat-tidak-mampu.show', $letter)
            ->with('success', "Surat Keterangan Tidak Mampu atas nama \"{$letter->nama_warga}\" berhasil dibuat.");
    }

    public function show(PovertyLetter $povertyLetter): Response
    {
        return Inertia::render('PovertyLetter/Show', [
            'letter' => PovertyLetterResource::make($povertyLetter->load('creator'))->resolve(),
        ]);
    }

    public function edit(PovertyLetter $povertyLetter): Response
    {
        return Inertia::render('PovertyLetter/Edit', [
            'letter' => PovertyLetterResource::make($povertyLetter)->resolve(),
        ]);
    }

    public function update(StorePovertyLetterRequest $request, PovertyLetter $povertyLetter)
    {
        $data = $request->validated();

        foreach (self::KOP_DEFAULTS as $key => $default) {
            if (empty($data[$key])) {
                $data[$key] = $povertyLetter->{$key} ?? $default;
            }
        }

        $uploads = [
            'logo_kiri'  => $request->file('logo_kiri'),
            'logo_kanan' => $request->file('logo_kanan'),
            'stempel'    => $request->file('stempel'),
        ];

        $paths = [];

        foreach ($uploads as $column => $file) {
            if ($file) {
                $oldPath = $povertyLetter->{$column . '_path'};
                if ($oldPath) {
                    Storage::disk('public')->delete($oldPath);
                }
                $paths[$column . '_path'] = $file->store('surat-tidak-mampu', 'public');
            }
        }

        if ($request->hasFile('tanda_tangan')) {
            if ($povertyLetter->tanda_tangan_path) {
                Storage::disk('public')->delete($povertyLetter->tanda_tangan_path);
            }
            $paths['tanda_tangan_path'] = $request->file('tanda_tangan')->store('surat-tidak-mampu', 'public');
        }

        $signers = $data['penandatangan_list'] ?? null;
        if ($signers && is_string($signers)) {
            $signers = json_decode($signers, true);
        }

        if (is_array($signers)) {
            $oldSigners = (array) ($povertyLetter->penandatangan_list ?? []);
            foreach ($signers as $i => &$signer) {
                $fileTt = $request->file("tanda_tangan_signer_{$i}");
                if ($fileTt) {
                    $signer['tanda_tangan_path'] = $fileTt->store('surat-tidak-mampu', 'public');
                } else {
                    $signer['tanda_tangan_path'] = $oldSigners[$i]['tanda_tangan_path'] ?? null;
                }
                unset($signer['tanda_tangan']);
            }
            unset($signer);
        }

        $povertyLetter->update(array_merge($data, $paths, [
            'penandatangan_list' => $signers,
        ]));

        return redirect()
            ->route('surat-tidak-mampu.show', $povertyLetter)
            ->with('success', "Surat Keterangan Tidak Mampu atas nama \"{$povertyLetter->nama_warga}\" berhasil diperbarui.");
    }

    public function destroy(PovertyLetter $povertyLetter)
    {
        $files = array_filter([
            $povertyLetter->logo_kiri_path,
            $povertyLetter->logo_kanan_path,
            $povertyLetter->stempel_path,
            $povertyLetter->tanda_tangan_path,
        ]);

        foreach ((array) ($povertyLetter->penandatangan_list ?? []) as $s) {
            if (!empty($s['tanda_tangan_path'])) $files[] = $s['tanda_tangan_path'];
            if (!empty($s['stempel_path']))      $files[] = $s['stempel_path'];
        }

        Storage::disk('public')->delete(array_values(array_unique(array_filter($files))));
        $povertyLetter->delete();

        return back()->with('success', 'Surat Keterangan Tidak Mampu berhasil dihapus.');
    }
}
