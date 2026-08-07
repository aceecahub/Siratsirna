<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKtpLetterRequest;
use App\Http\Resources\KtpLetterResource;
use App\Models\KtpLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class KtpLetterController extends Controller
{
    public function index(Request $request): Response
    {
        $query = KtpLetter::query();

        if ($search = $request->string('search')->trim()) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                    ->orWhere('nama_warga', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $letters = $query->latest('tanggal_surat')->paginate(10)->withQueryString();

        return Inertia::render('KtpLetter/Index', [
            'letters' => KtpLetterResource::collection($letters),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('KtpLetter/Create');
    }

    public function store(StoreKtpLetterRequest $request)
    {
        $data = $request->validated();

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
                $p = $file->store('surat-pengantar-ktp', 'public');
                $paths[$col . '_path'] = $p;
                $allPaths[]            = $p;
            }
        }

        try {
            $letter = KtpLetter::create([
                ...$data,
                ...$paths,
                'created_by' => $request->user()?->id,
            ]);
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($allPaths);
            throw $e;
        }

        return redirect()
            ->route('surat-pengantar-ktp.show', $letter)
            ->with('success', "Surat Pengantar Pencetakan E-KTP atas nama \"{$letter->nama_warga}\" berhasil dibuat.");
    }

    public function show(KtpLetter $ktpLetter): Response
    {
        return Inertia::render('KtpLetter/Show', [
            'letter' => KtpLetterResource::make($ktpLetter->load('creator'))->resolve(),
        ]);
    }

    public function destroy(KtpLetter $ktpLetter)
    {
        Storage::disk('public')->delete(array_filter([
            $ktpLetter->logo_kiri_path,
            $ktpLetter->logo_kanan_path,
            $ktpLetter->tanda_tangan_path,
            $ktpLetter->stempel_path,
        ]));

        $ktpLetter->delete();

        return back()->with('success', 'Surat Pengantar Pencetakan E-KTP berhasil dihapus.');
    }
}
