<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KtpLetterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,

            // Kop
            'kabupaten'  => $this->kabupaten,
            'kecamatan'  => $this->kecamatan,
            'nama_desa'  => $this->nama_desa,
            'alamat'     => $this->alamat,
            'kode_pos'   => $this->kode_pos,
            'kontak'     => $this->kontak,
            'email_desa' => $this->email_desa,
            'logo_kiri'  => $this->logo_kiri_url,
            'logo_kanan' => $this->logo_kanan_url,

            // Surat
            'nomor_surat'   => $this->nomor_surat,
            'tanggal_surat' => $this->tanggal_surat?->toDateString(),

            // Data Warga
            'nik'               => $this->nik,
            'nama_warga'        => $this->nama_warga,
            'tempat_lahir'      => $this->tempat_lahir,
            'tanggal_lahir'     => $this->tanggal_lahir?->toDateString(),
            'warga_negara'      => $this->warga_negara,
            'jenis_kelamin'     => $this->jenis_kelamin,
            'agama'             => $this->agama,
            'status_perkawinan' => $this->status_perkawinan,
            'pekerjaan'         => $this->pekerjaan,
            'alamat_warga'      => $this->alamat_warga,
            'rt'                => $this->rt,
            'rw'                => $this->rw,
            'dusun'             => $this->dusun,

            // Teks tambahan & Penutup
            'keterangan_tambahan' => $this->keterangan_tambahan,
            'kalimat_penutup'     => $this->kalimat_penutup,

            // TTD
            'label_atasan'          => $this->label_atasan,
            'jabatan_penandatangan' => $this->jabatan_penandatangan,
            'nama_penandatangan'    => $this->nama_penandatangan,
            'nip_niapd'             => $this->nip_niapd,
            'tanda_tangan'          => $this->tanda_tangan_url,
            'stempel'               => $this->stempel_url,

            'created_by' => $this->whenLoaded('creator', fn () => $this->creator?->name),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
