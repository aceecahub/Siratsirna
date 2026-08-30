<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHeirLetterRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            // Kop — nullable (statis)
            'kabupaten'  => ['nullable', 'string', 'max:191'],
            'kecamatan'  => ['nullable', 'string', 'max:191'],
            'nama_desa'  => ['nullable', 'string', 'max:191'],
            'alamat'     => ['nullable', 'string'],
            'kode_pos'   => ['nullable', 'string', 'max:10'],
            'kontak'     => ['nullable', 'string', 'max:191'],
            'email_desa' => ['nullable', 'string', 'max:191'],
            'logo_kiri'  => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'logo_kanan' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],

            // Surat
            'nomor_surat'   => ['required', 'string', 'max:191'],
            'tanggal_surat' => ['required', 'date'],

            // Kades
            'nama_kades'    => ['nullable', 'string', 'max:191'],
            'jabatan_kades' => ['nullable', 'string', 'max:191'],

            // Almarhum
            'nama_almarhum'          => ['required', 'string', 'max:191'],
            'nik_almarhum'           => ['nullable', 'string', 'max:20'],
            'jenis_kelamin_almarhum' => ['nullable', 'string', 'max:20'],
            'tempat_lahir_almarhum'  => ['nullable', 'string', 'max:191'],
            'tanggal_lahir_almarhum' => ['nullable', 'date'],
            'agama_almarhum'         => ['nullable', 'string', 'max:50'],
            'alamat_almarhum'        => ['nullable', 'string'],
            'rt_almarhum'            => ['nullable', 'string', 'max:10'],
            'rw_almarhum'            => ['nullable', 'string', 'max:10'],
            'dusun_almarhum'         => ['nullable', 'string', 'max:191'],

            // Info Meninggal
            'hari_meninggal'    => ['nullable', 'string', 'max:50'],
            'tanggal_meninggal' => ['nullable', 'date'],
            'tempat_meninggal'  => ['nullable', 'string', 'max:191'],
            'lokasi_meninggal'  => ['nullable', 'string', 'max:191'],
            'sebab_meninggal'   => ['nullable', 'string', 'max:191'],
            'tpu_pemakaman'     => ['nullable', 'string', 'max:191'],

            // JSON Ahli Waris & Saksi — terima array langsung atau JSON string
            'ahli_waris_list'   => ['nullable'],
            'saksi_list'        => ['nullable'],

            // Yang Menyatakan
            'nama_yang_menyatakan'  => ['nullable', 'string', 'max:191'],
            'label_yang_menyatakan' => ['nullable', 'string', 'max:191'],

            // Pengesahan Camat & Kades
            'no_reg_camat'  => ['nullable', 'string', 'max:191'],
            'jabatan_camat' => ['nullable', 'string', 'max:191'],
            'nama_camat'    => ['nullable', 'string', 'max:191'],

            'tanda_tangan' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'stempel'      => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
        ];
    }
}
