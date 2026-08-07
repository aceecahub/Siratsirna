<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssistanceLetterRequest;
use App\Http\Resources\AssistanceLetterResource;
use App\Models\AssistanceLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class AssistanceLetterController extends Controller
{
    public function index(Request $request): Response
    {
        $query = AssistanceLetter::query();

        if ($search = $request->string('search')->trim()) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                    ->orWhere('nama_pasien', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $letters = $query->latest('tanggal_surat')->paginate(10)->withQueryString();

        return Inertia::render('AssistanceLetter/Index', [
            'letters' => AssistanceLetterResource::collection($letters),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('AssistanceLetter/Create');
    }

    public function store(StoreAssistanceLetterRequest $request)
    {
        $data = $request->validated();

        $lampiranList = isset($data['lampiran_list']) && is_string($data['lampiran_list'])
            ? json_decode($data['lampiran_list'], true)
            : ($data['lampiran_list'] ?? []);

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
                $p = $file->store('surat-permohonan-bantuan', 'public');
                $paths[$col . '_path'] = $p;
                $allPaths[]            = $p;
            }
        }

        try {
            $letter = AssistanceLetter::create([
                ...$data,
                'lampiran_list' => $lampiranList,
                ...$paths,
                'created_by' => $request->user()?->id,
            ]);
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($allPaths);
            throw $e;
        }

        return redirect()
            ->route('surat-permohonan-bantuan.show', $letter)
            ->with('success', "Surat Permohonan Bantuan atas nama \"{$letter->nama_pasien}\" berhasil dibuat.");
    }

    public function show(AssistanceLetter $assistanceLetter): Response
    {
        return Inertia::render('AssistanceLetter/Show', [
            'letter' => AssistanceLetterResource::make($assistanceLetter->load('creator'))->resolve(),
        ]);
    }

    public function destroy(AssistanceLetter $assistanceLetter)
    {
        Storage::disk('public')->delete(array_filter([
            $assistanceLetter->logo_kiri_path,
            $assistanceLetter->logo_kanan_path,
            $assistanceLetter->tanda_tangan_path,
            $assistanceLetter->stempel_path,
        ]));

        $assistanceLetter->delete();

        return back()->with('success', 'Surat Permohonan Bantuan berhasil dihapus.');
    }
}
