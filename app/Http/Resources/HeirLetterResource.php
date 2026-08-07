<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HeirLetterResource extends JsonResource
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

            // Kades
            'nama_kades'    => $this->nama_kades,
            'jabatan_kades' => $this->jabatan_kades,

            // Almarhum
            'nama_almarhum'          => $this->nama_almarhum,
            'nik_almarhum'           => $this->nik_almarhum,
            'jenis_kelamin_almarhum' => $this->jenis_kelamin_almarhum,
            'tempat_lahir_almarhum'  => $this->tempat_lahir_almarhum,
            'tanggal_lahir_almarhum' => $this->tanggal_lahir_almarhum?->toDateString(),
            'agama_almarhum'         => $this->agama_almarhum,
            'alamat_almarhum'        => $this->alamat_almarhum,
            'rt_almarhum'            => $this->rt_almarhum,
            'rw_almarhum'            => $this->rw_almarhum,
            'dusun_almarhum'         => $this->dusun_almarhum,

            // Info Meninggal
            'hari_meninggal'    => $this->hari_meninggal,
            'tanggal_meninggal' => $this->tanggal_meninggal?->toDateString(),
            'tempat_meninggal'  => $this->tempat_meninggal,
            'sebab_meninggal'   => $this->sebab_meninggal,
            'tpu_pemakaman'     => $this->tpu_pemakaman,

            // Dynamic Lists
            'ahli_waris_list' => $this->ahli_waris_list ?? [],
            'saksi_list'      => $this->saksi_list ?? [],

            // Yang Menyatakan
            'nama_yang_menyatakan'  => $this->nama_yang_menyatakan,
            'label_yang_menyatakan' => $this->label_yang_menyatakan,

            // Pengesahan
            'no_reg_camat'  => $this->no_reg_camat,
            'jabatan_camat' => $this->jabatan_camat,
            'nama_camat'    => $this->nama_camat,

            'tanda_tangan' => $this->tanda_tangan_url,
            'stempel'      => $this->stempel_url,

            'created_by' => $this->whenLoaded('creator', fn () => $this->creator?->name),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
