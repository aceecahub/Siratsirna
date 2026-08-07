<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssistanceLetterResource extends JsonResource
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

            // Header Kiri & Kanan
            'nomor_surat'    => $this->nomor_surat,
            'tanggal_surat'  => $this->tanggal_surat?->toDateString(),
            'sifat_surat'    => $this->sifat_surat,
            'lampiran_surat' => $this->lampiran_surat,
            'perihal_surat'  => $this->perihal_surat,

            'tujuan_nama' => $this->tujuan_nama,
            'tujuan_cq'   => $this->tujuan_cq,
            'tujuan_di'   => $this->tujuan_di,

            // Identitas Pasien
            'no_kk'         => $this->no_kk,
            'nik'           => $this->nik,
            'nama_pasien'   => $this->nama_pasien,
            'jenis_kelamin' => $this->jenis_kelamin,
            'tempat_lahir'  => $this->tempat_lahir,
            'tanggal_lahir' => $this->tanggal_lahir?->toDateString(),
            'pekerjaan'     => $this->pekerjaan,
            'alamat_pasien' => $this->alamat_pasien,
            'rt'            => $this->rt,
            'rw'            => $this->rw,
            'dusun'         => $this->dusun,
            'id_dtks'       => $this->id_dtks,

            // Isi Permohonan & Lampiran
            'teks_permohonan' => $this->teks_permohonan,
            'lampiran_list'   => $this->lampiran_list ?? [],
            'kalimat_penutup' => $this->kalimat_penutup,

            // TTD 3 Kolom
            'jabatan_ttd_kiri'   => $this->jabatan_ttd_kiri,
            'nama_ttd_kiri'      => $this->nama_ttd_kiri,

            'label_ttd_tengah'   => $this->label_ttd_tengah,
            'jabatan_ttd_tengah' => $this->jabatan_ttd_tengah,
            'nama_ttd_tengah'    => $this->nama_ttd_tengah,

            'label_ttd_kanan'   => $this->label_ttd_kanan,
            'jabatan_ttd_kanan' => $this->jabatan_ttd_kanan,
            'nama_ttd_kanan'    => $this->nama_ttd_kanan,

            'tanda_tangan' => $this->tanda_tangan_url,
            'stempel'      => $this->stempel_url,

            'created_by' => $this->whenLoaded('creator', fn () => $this->creator?->name),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
