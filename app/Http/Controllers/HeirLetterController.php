<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHeirLetterRequest;
use App\Http\Resources\HeirLetterResource;
use App\Models\HeirLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class HeirLetterController extends Controller
{
    public function index(Request $request): Response
    {
        $query = HeirLetter::query();

        if ($search = $request->string('search')->trim()) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                    ->orWhere('nama_almarhum', 'like', "%{$search}%")
                    ->orWhere('nama_yang_menyatakan', 'like', "%{$search}%");
            });
        }

        $letters = $query->latest('tanggal_surat')->paginate(10)->withQueryString();

        return Inertia::render('HeirLetter/Index', [
            'letters' => HeirLetterResource::collection($letters),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('HeirLetter/Create');
    }

    public function store(StoreHeirLetterRequest $request)
    {
        $data = $request->validated();

        // Decode JSON arrays
        $ahliWarisList = is_string($data['ahli_waris_list'])
            ? json_decode($data['ahli_waris_list'], true)
            : $data['ahli_waris_list'];

        $saksiList = isset($data['saksi_list']) && is_string($data['saksi_list'])
            ? json_decode($data['saksi_list'], true)
            : ($data['saksi_list'] ?? []);

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
                $p = $file->store('surat-ahli-waris', 'public');
                $paths[$col . '_path'] = $p;
                $allPaths[]            = $p;
            }
        }

        try {
            $letter = HeirLetter::create([
                ...$data,
                'ahli_waris_list' => $ahliWarisList,
                'saksi_list'      => $saksiList,
                ...$paths,
                'created_by' => $request->user()?->id,
            ]);
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($allPaths);
            throw $e;
        }

        return redirect()
            ->route('surat-ahli-waris.show', $letter)
            ->with('success', "Surat Keterangan Ahli Waris Almarhum \"{$letter->nama_almarhum}\" berhasil dibuat.");
    }

    public function show(HeirLetter $heirLetter): Response
    {
        return Inertia::render('HeirLetter/Show', [
            'letter' => HeirLetterResource::make($heirLetter->load('creator'))->resolve(),
        ]);
    }

    public function destroy(HeirLetter $heirLetter)
    {
        Storage::disk('public')->delete(array_filter([
            $heirLetter->logo_kiri_path,
            $heirLetter->logo_kanan_path,
            $heirLetter->tanda_tangan_path,
            $heirLetter->stempel_path,
        ]));

        $heirLetter->delete();

        return back()->with('success', 'Surat Keterangan Ahli Waris berhasil dihapus.');
    }
}
