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

        // ── Upload TTD & stempel per signer ───────────────────────
        $signerJson = $request->input('penandatangan_list');
        $signers    = is_string($signerJson) ? json_decode($signerJson, true) : (array) $signerJson;

        $allPaths = array_values(array_filter(array_values($paths)));

        foreach ($signers as $idx => &$signer) {
            // tanda_tangan_signer_{idx}
            $ttdKey = "tanda_tangan_signer_{$idx}";
            if ($request->hasFile($ttdKey)) {
                $p = $request->file($ttdKey)->store('surat-tidak-mampu', 'public');
                $signer['tanda_tangan_path'] = $p;
                $allPaths[]                  = $p;
            }

            $stempelKey = "stempel_signer_{$idx}";
            if ($request->hasFile($stempelKey)) {
                $p = $request->file($stempelKey)->store('surat-tidak-mampu', 'public');
                $signer['stempel_path'] = $p;
                $allPaths[]             = $p;
            }

            // Hapus field sementara agar tidak disimpan mentah
            unset($signer['tanda_tangan'], $signer['stempel']);
        }
        unset($signer);

        try {
            $letter = PovertyLetter::create([
                ...$data,
                ...$paths,
                'penandatangan_list' => $signers,
                'created_by'         => $request->user()?->id,
            ]);
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

    public function destroy(PovertyLetter $povertyLetter)
    {
        $files = array_filter([
            $povertyLetter->logo_kiri_path,
            $povertyLetter->logo_kanan_path,
            $povertyLetter->stempel_path,
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
