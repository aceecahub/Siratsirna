<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class OutgoingMailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $penandatanganList = $this->penandatangan_list;

        if (is_array($penandatanganList) && count($penandatanganList) > 0) {
            $formattedList = array_map(function ($item) {
                $ttdPath = $item['tanda_tangan_path'] ?? null;
                $ttdUrl = $ttdPath ? Storage::disk('public')->url($ttdPath) : ($item['tanda_tangan'] ?? null);

                $stempelPath = $item['stempel_path'] ?? null;
                $stempelUrl = $stempelPath ? Storage::disk('public')->url($stempelPath) : ($item['stempel'] ?? null);

                return [
                    'peran' => $item['peran'] ?? 'kanan',
                    'label_mengetahui' => $item['label_mengetahui'] ?? '',
                    'jabatan' => $item['jabatan'] ?? '',
                    'nama' => $item['nama'] ?? '',
                    'nip_niapd' => $item['nip_niapd'] ?? '',
                    'tanda_tangan' => $ttdUrl,
                    'stempel' => $stempelUrl,
                ];
            }, $penandatanganList);
        } else {
            $formattedList = [
                [
                    'peran' => 'kanan',
                    'label_mengetahui' => '',
                    'jabatan' => $this->jabatan_penandatangan ?? '',
                    'nama' => $this->nama_penandatangan ?? '',
                    'nip_niapd' => $this->nip_niapd ?? '',
                    'tanda_tangan' => $this->tanda_tangan_url,
                    'stempel' => $this->stempel_url,
                ],
            ];
        }

        return [
            'id' => $this->id,
            'kabupaten' => $this->kabupaten,
            'kecamatan' => $this->kecamatan,
            'nama_desa' => $this->nama_desa,
            'alamat' => $this->alamat,
            'kode_pos' => $this->kode_pos,
            'kontak' => $this->kontak,
            'email_desa' => $this->email_desa,
            'nomor_surat' => $this->nomor_surat,
            'sifat' => $this->sifat,
            'lampiran' => $this->lampiran,
            'perihal' => $this->perihal,
            'tanggal_surat' => $this->tanggal_surat?->toDateString(),
            'tujuan' => $this->tujuan,
            'pembuka' => $this->pembuka,
            'isi_surat' => $this->isi_surat,
            'penutup' => $this->penutup,
            'jabatan_penandatangan' => $this->jabatan_penandatangan,
            'nama_penandatangan' => $this->nama_penandatangan,
            'nip_niapd' => $this->nip_niapd,
            'logo_kiri' => $this->logo_kiri_url,
            'logo_kanan' => $this->logo_kanan_url,
            'tanda_tangan' => $this->tanda_tangan_url,
            'stempel' => $this->stempel_url,
            'penandatangan_list' => $formattedList,
            'created_by' => $this->whenLoaded('creator', fn () => $this->creator?->name),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
