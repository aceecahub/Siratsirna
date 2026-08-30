<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOutgoingMailRequest;
use App\Http\Resources\OutgoingMailResource;
use App\Models\OutgoingMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class OutgoingMailController extends Controller
{
    private const KOP_DEFAULTS = [
        'kabupaten' => 'Tasikmalaya',
        'kecamatan' => 'Cigalontang',
        'nama_desa' => 'Sirnaputra',
        'alamat'    => 'Jl. Raya Sirnaputra No. 01',
        'kode_pos'  => '46463',
    ];

    /**
     * Display a paginated listing of generated outgoing mails.
     */
    public function index(Request $request): Response
    {
        $query = OutgoingMail::query();

        if ($search = $request->string('search')->trim()) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                    ->orWhere('perihal', 'like', "%{$search}%")
                    ->orWhere('tujuan', 'like', "%{$search}%");
            });
        }

        $mails = $query
            ->latest('tanggal_surat')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('OutgoingMail/Index', [
            'mails' => OutgoingMailResource::collection($mails),
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the dynamic letter generator with live preview.
     */
    public function create(): Response
    {
        return Inertia::render('OutgoingMail/Create', [
            'templates' => $this->templates(),
        ]);
    }

    /**
     * Persist a generated outgoing letter along with its assets.
     */
    public function store(StoreOutgoingMailRequest $request)
    {
        $data = $request->validated();

        foreach (self::KOP_DEFAULTS as $key => $default) {
            if (empty($data[$key])) {
                $data[$key] = $default;
            }
        }

        $uploads = [
            'logo_kiri' => $request->file('logo_kiri'),
            'logo_kanan' => $request->file('logo_kanan'),
            'tanda_tangan' => $request->file('tanda_tangan'),
            'stempel' => $request->file('stempel'),
        ];

        $paths = [];
        $cleanupPaths = [];
        foreach ($uploads as $column => $file) {
            if ($file) {
                $savedPath = $file->store('surat-keluar', 'public');
                $paths[$column.'_path'] = $savedPath;
                $cleanupPaths[] = $savedPath;
            }
        }

        $penandatanganList = $request->input('penandatangan_list');
        if (is_string($penandatanganList)) {
            $penandatanganList = json_decode($penandatanganList, true);
        }

        if (is_array($penandatanganList) && count($penandatanganList) > 0) {
            foreach ($penandatanganList as $index => &$signer) {
                // Signature file upload
                $ttdFile = $request->file("penandatangan_list.{$index}.tanda_tangan_file")
                    ?? $request->file("penandatangan_file_{$index}");

                if ($ttdFile) {
                    $savedPath = $ttdFile->store('surat-keluar', 'public');
                    $signer['tanda_tangan_path'] = $savedPath;
                    $cleanupPaths[] = $savedPath;
                }

                // Stamp file upload per signer
                $stempelFile = $request->file("penandatangan_list.{$index}.stempel_file")
                    ?? $request->file("stempel_file_{$index}");

                if ($stempelFile) {
                    $savedPath = $stempelFile->store('surat-keluar', 'public');
                    $signer['stempel_path'] = $savedPath;
                    $cleanupPaths[] = $savedPath;
                }
            }
            unset($signer);

            $data['penandatangan_list'] = $penandatanganList;

            $primary = $penandatanganList[0];
            $data['jabatan_penandatangan'] = $data['jabatan_penandatangan'] ?? ($primary['jabatan'] ?? '');
            $data['nama_penandatangan'] = $data['nama_penandatangan'] ?? ($primary['nama'] ?? '');
            $data['nip_niapd'] = $data['nip_niapd'] ?? ($primary['nip_niapd'] ?? null);
            if (isset($primary['tanda_tangan_path'])) {
                $paths['tanda_tangan_path'] = $primary['tanda_tangan_path'];
            }
        } else {
            $data['jabatan_penandatangan'] = $data['jabatan_penandatangan'] ?? '';
            $data['nama_penandatangan'] = $data['nama_penandatangan'] ?? '';
        }

        try {
            $mail = OutgoingMail::create([
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
            ->route('surat-keluar.show', $mail)
            ->with('success', "Surat keluar \"{$mail->perihal}\" berhasil dibuat dan siap dicetak.");
    }

    /**
     * Display a single letter ready for print / export.
     */
    public function show(OutgoingMail $outgoingMail): Response
    {
        return Inertia::render('OutgoingMail/Show', [
            'mail' => OutgoingMailResource::make($outgoingMail->load('creator'))->resolve(),
        ]);
    }

    /**
     * Remove a generated letter and its stored assets.
     */
    public function destroy(OutgoingMail $outgoingMail)
    {
        $files = [
            $outgoingMail->logo_kiri_path,
            $outgoingMail->logo_kanan_path,
            $outgoingMail->tanda_tangan_path,
            $outgoingMail->stempel_path,
        ];

        if (is_array($outgoingMail->penandatangan_list)) {
            foreach ($outgoingMail->penandatangan_list as $signer) {
                if (!empty($signer['tanda_tangan_path'])) {
                    $files[] = $signer['tanda_tangan_path'];
                }
                if (!empty($signer['stempel_path'])) {
                    $files[] = $signer['stempel_path'];
                }
            }
        }

        Storage::disk('public')->delete(array_values(array_filter(array_unique($files))));
        $outgoingMail->delete();

        return back()->with('success', 'Surat keluar berhasil dihapus.');
    }

    /**
     * Standard opening-sentence templates for the letter body.
     *
     * @return array<int, array{label: string, text: string}>
     */
    protected function templates(): array
    {
        return [
            [
                'label' => 'Umum (Keterangan)',
                'text' => 'Yang bertanda tangan di bawah ini, {jabatan} {nama_desa}, dengan ini menerangkan bahwa:',
            ],
            [
                'label' => 'Pengantar',
                'text' => 'Yang bertanda tangan di bawah ini, {jabatan} {nama_desa}, dengan ini mengantarkan:',
            ],
            [
                'label' => 'Permohonan',
                'text' => 'Yang bertanda tangan di bawah ini, {jabatan} {nama_desa}, dengan ini mengajukan permohonan kepada Yth. {tujuan}, untuk:',
            ],
            [
                'label' => 'Sehubungan dengan',
                'text' => 'Sehubungan dengan pelaksanaan kegiatan di Desa {nama_desa}, bersama ini kami sampaikan:',
            ],
            [
                'label' => 'Menindaklanjuti',
                'text' => 'Menindaklanjuti surat Nomor : ... tanggal ..., dengan ini kami sampaikan hal-hal sebagai berikut:',
            ],
            [
                'label' => 'Undangan Rapat',
                'text' => 'Dengan hormat, sehubungan dengan akan dilaksanakannya kegiatan rapat di Desa {nama_desa}, bersama ini kami mengundang Bapak/Ibu untuk hadir pada:',
            ],
            [
                'label' => 'Pemberitahuan',
                'text' => 'Bersama ini kami beritahukan kepada seluruh warga masyarakat Desa {nama_desa} bahwa:',
            ],
        ];
    }
}
