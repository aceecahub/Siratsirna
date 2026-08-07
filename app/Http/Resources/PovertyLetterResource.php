<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PovertyLetterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Resolve tanda_tangan & stempel per signer
        $signers = [];
        foreach ((array) ($this->penandatangan_list ?? []) as $item) {
            $ttdPath    = $item['tanda_tangan_path'] ?? null;
            $ttdUrl     = $ttdPath ? Storage::disk('public')->url($ttdPath) : ($item['tanda_tangan'] ?? null);

            $stempelPath = $item['stempel_path'] ?? null;
            $stempelUrl  = $stempelPath ? Storage::disk('public')->url($stempelPath) : ($item['stempel'] ?? null);

            $signers[] = [
                'peran'            => $item['peran']            ?? 'kanan',
                'label_mengetahui' => $item['label_mengetahui'] ?? '',
                'jabatan'          => $item['jabatan']          ?? '',
                'nama'             => $item['nama']             ?? '',
                'nip_niapd'        => $item['nip_niapd']        ?? '',
                'tanda_tangan'     => $ttdUrl,
                'stempel'          => $stempelUrl,
            ];
        }

        return [
            'id'         => $this->id,

            // Kop surat
            'kabupaten'  => $this->kabupaten,
            'kecamatan'  => $this->kecamatan,
            'nama_desa'  => $this->nama_desa,
            'alamat'     => $this->alamat,
            'kode_pos'   => $this->kode_pos,
            'kontak'     => $this->kontak,
            'email_desa' => $this->email_desa,
            'logo_kiri'  => $this->logo_kiri_url,
            'logo_kanan' => $this->logo_kanan_url,

            // Atribut
            'nomor_surat'   => $this->nomor_surat,
            'tanggal_surat' => $this->tanggal_surat?->toDateString(),

            // Data warga
            'nama_warga'   => $this->nama_warga,
            'tempat_lahir' => $this->tempat_lahir,
            'tanggal_lahir'=> $this->tanggal_lahir?->toDateString(),
            'pekerjaan'    => $this->pekerjaan,
            'alamat_warga' => $this->alamat_warga,
            'rt'           => $this->rt,
            'rw'           => $this->rw,
            'dusun'        => $this->dusun,

            // Isi
            'keterangan' => $this->keterangan,

            // Stempel watermark
            'stempel' => $this->stempel_url,

            // Penandatangan
            'penandatangan_list' => $signers,

            'created_by' => $this->whenLoaded('creator', fn () => $this->creator?->name),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
