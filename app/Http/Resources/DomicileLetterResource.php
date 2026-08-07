<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DomicileLetterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,

            // Kop surat
            'kabupaten'   => $this->kabupaten,
            'kecamatan'   => $this->kecamatan,
            'nama_desa'   => $this->nama_desa,
            'alamat'      => $this->alamat,
            'kode_pos'    => $this->kode_pos,
            'kontak'      => $this->kontak,
            'email_desa'  => $this->email_desa,
            'logo_kiri'   => $this->logo_kiri_url,
            'logo_kanan'  => $this->logo_kanan_url,

            // Atribut surat
            'nomor_surat'   => $this->nomor_surat,
            'tanggal_surat' => $this->tanggal_surat?->toDateString(),

            // Data warga
            'nik'           => $this->nik,
            'nama_warga'    => $this->nama_warga,
            'tempat_lahir'  => $this->tempat_lahir,
            'tanggal_lahir' => $this->tanggal_lahir?->toDateString(),
            'jenis_kelamin' => $this->jenis_kelamin,
            'warga_negara'  => $this->warga_negara,
            'agama'         => $this->agama,
            'pekerjaan'     => $this->pekerjaan,
            'alamat_warga'  => $this->alamat_warga,
            'rt'            => $this->rt,
            'rw'            => $this->rw,
            'dusun'         => $this->dusun,

            // Keterangan
            'keterangan_domisili' => $this->keterangan_domisili,
            'catatan_ektp'        => $this->catatan_ektp,

            // Tanda tangan
            'jabatan_penandatangan' => $this->jabatan_penandatangan,
            'nama_penandatangan'    => $this->nama_penandatangan,
            'nip_niapd'             => $this->nip_niapd,
            'tanda_tangan'          => $this->tanda_tangan_url,
            'stempel'               => $this->stempel_url,

            'created_by' => $this->whenLoaded('creator', fn() => $this->creator?->name),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
