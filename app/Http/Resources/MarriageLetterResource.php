<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarriageLetterResource extends JsonResource
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

            // Pemohon
            'nama_pemohon'  => $this->nama_pemohon,
            'tempat_lahir'  => $this->tempat_lahir,
            'tanggal_lahir' => $this->tanggal_lahir?->toDateString(),
            'pekerjaan'     => $this->pekerjaan,
            'alamat_pemohon'=> $this->alamat_pemohon,
            'rt'            => $this->rt,
            'rw'            => $this->rw,
            'dusun'         => $this->dusun,

            // Nikah
            'jenis_nikah'      => $this->jenis_nikah,
            'hari_nikah'       => $this->hari_nikah,
            'tanggal_nikah'    => $this->tanggal_nikah?->toDateString(),
            'keterangan_nikah' => $this->keterangan_nikah,

            // Pasangan
            'nama_pasangan'          => $this->nama_pasangan,
            'tempat_lahir_pasangan'  => $this->tempat_lahir_pasangan,
            'tanggal_lahir_pasangan' => $this->tanggal_lahir_pasangan?->toDateString(),
            'alamat_pasangan'        => $this->alamat_pasangan,

            // Penutup
            'kalimat_penutup' => $this->kalimat_penutup,

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
