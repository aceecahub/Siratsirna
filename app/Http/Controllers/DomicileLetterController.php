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
    /**
     * Display a paginated listing of domicile letters.
     */
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

    /**
     * Show the form for creating a new domicile letter.
     */
    public function create(): Response
    {
        return Inertia::render('DomicileLetter/Create');
    }

    /**
     * Store a newly created domicile letter.
     */
    public function store(StoreDomicileLetterRequest $request)
    {
        $data = $request->validated();

        $uploads = [
            'logo_kiri'   => $request->file('logo_kiri'),
            'logo_kanan'  => $request->file('logo_kanan'),
            'tanda_tangan' => $request->file('tanda_tangan'),
            'stempel'     => $request->file('stempel'),
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

    /**
     * Display a single domicile letter ready for print / export.
     */
    public function show(DomicileLetter $domicileLetter): Response
    {
        return Inertia::render('DomicileLetter/Show', [
            'letter' => DomicileLetterResource::make($domicileLetter->load('creator'))->resolve(),
        ]);
    }

    /**
     * Remove a domicile letter and its stored assets.
     */
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
