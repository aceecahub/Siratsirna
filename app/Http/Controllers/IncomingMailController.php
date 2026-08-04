<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncomingMailRequest;
use App\Http\Resources\IncomingMailResource;
use App\Models\IncomingMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class IncomingMailController extends Controller
{
    /**
     * Display a paginated, filterable listing of incoming mails.
     */
    public function index(Request $request): Response
    {
        $query = IncomingMail::query();

        if ($search = $request->string('search')->trim()) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_agenda', 'like', "%{$search}%")
                    ->orWhere('nomor_surat_asal', 'like', "%{$search}%")
                    ->orWhere('pengirim', 'like', "%{$search}%")
                    ->orWhere('perihal', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->string('kategori'));
        }

        if ($request->filled('tanggal_awal')) {
            $query->where('tanggal_diterima', '>=', $request->string('tanggal_awal'));
        }

        if ($request->filled('tanggal_akhir')) {
            $query->where('tanggal_diterima', '<=', $request->string('tanggal_akhir'));
        }

        $mails = $query
            ->latest('tanggal_diterima')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('IncomingMail/Index', [
            'mails' => IncomingMailResource::collection($mails),
            'filters' => $request->only(['search', 'kategori', 'tanggal_awal', 'tanggal_akhir']),
            'categories' => IncomingMail::CATEGORIES,
        ]);
    }

    /**
     * Store a newly uploaded incoming mail scan.
     */
    public function store(StoreIncomingMailRequest $request)
    {
        $data = $request->validated();

        $file = $request->file('file');
        $path = $file->store('surat-masuk', 'public');
        $createdBy = $request->user()?->id;

        try {
            $mail = DB::transaction(function () use ($data, $file, $path, $createdBy) {
                return IncomingMail::create([
                    ...$data,
                    'nomor_agenda' => $this->generateNomorAgenda(),
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_extension' => strtolower($file->getClientOriginalExtension()) ?: 'pdf',
                    'file_size' => $file->getSize(),
                    'created_by' => $createdBy,
                ]);
            });
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($path);

            throw $e;
        }

        return back()->with('success', "Surat masuk \"{$mail->perihal}\" berhasil diarsipkan dengan nomor agenda {$mail->nomor_agenda}.");
    }

    /**
     * Remove an incoming mail record and its stored file.
     */
    public function destroy(IncomingMail $incomingMail)
    {
        Storage::disk('public')->delete($incomingMail->file_path);
        $incomingMail->delete();

        return back()->with('success', 'Surat masuk berhasil dihapus.');
    }

    /**
     * Stream the original scan file for preview or download.
     */
    public function download(IncomingMail $incomingMail): StreamedResponse
    {
        return Storage::disk('public')->download($incomingMail->file_path, $incomingMail->file_name);
    }

    /**
     * Generate the internal agenda number: ARS-MASUK/[TAHUN]/[INCREMENT_PER_TAHUN].
     */
    protected function generateNomorAgenda(): string
    {
        $year = now()->year;

        return DB::transaction(function () use ($year) {
            $last = IncomingMail::whereYear('created_at', $year)
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            $increment = $last ? (int) Str::afterLast($last->nomor_agenda, '/') + 1 : 1;

            return 'ARS-MASUK/'.$year.'/'.str_pad((string) $increment, 3, '0', STR_PAD_LEFT);
        });
    }
}
