<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePovertyLetterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Kop surat — nullable (statis)
            'kabupaten'  => ['nullable', 'string', 'max:191'],
            'kecamatan'  => ['nullable', 'string', 'max:191'],
            'nama_desa'  => ['nullable', 'string', 'max:191'],
            'alamat'     => ['nullable', 'string'],
            'kode_pos'   => ['nullable', 'string', 'max:10'],
            'kontak'     => ['nullable', 'string', 'max:191'],
            'email_desa' => ['nullable', 'string', 'max:191'],
            'logo_kiri'  => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'logo_kanan' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],

            // Atribut surat
            'nomor_surat'   => ['required', 'string', 'max:191'],
            'tanggal_surat' => ['required', 'date'],

            // Data warga
            'nik'          => ['nullable', 'string', 'max:50'],
            'nama_warga'   => ['required', 'string', 'max:191'],
            'tempat_lahir' => ['required', 'string', 'max:191'],
            'tanggal_lahir'=> ['required', 'date'],
            'jenis_kelamin'=> ['nullable', 'string', 'max:20'],
            'warga_negara' => ['nullable', 'string', 'max:50'],
            'agama'        => ['nullable', 'string', 'max:50'],
            'pekerjaan'    => ['required', 'string', 'max:191'],
            'alamat_warga' => ['nullable', 'string'],
            'rt'           => ['nullable', 'string', 'max:10'],
            'rw'           => ['nullable', 'string', 'max:10'],
            'dusun'        => ['nullable', 'string', 'max:191'],

            // Isi
            'keterangan' => ['nullable', 'string'],

            // Penandatangan — accept both JSON string and plain fields
            'penandatangan_list'    => ['nullable'],
            'jabatan_penandatangan' => ['nullable', 'string', 'max:191'],
            'nama_penandatangan'    => ['nullable', 'string', 'max:191'],
            'nip_niapd'             => ['nullable', 'string', 'max:191'],

            // Stempel & TTD
            'stempel'      => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'tanda_tangan' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
        ];
    }
}
