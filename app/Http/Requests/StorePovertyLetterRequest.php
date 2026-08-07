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
            // Kop surat
            'kabupaten'  => ['required', 'string', 'max:191'],
            'kecamatan'  => ['required', 'string', 'max:191'],
            'nama_desa'  => ['required', 'string', 'max:191'],
            'alamat'     => ['required', 'string'],
            'kode_pos'   => ['nullable', 'string', 'max:10'],
            'kontak'     => ['nullable', 'string', 'max:191'],
            'email_desa' => ['nullable', 'string', 'max:191'],
            'logo_kiri'  => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:5120'],
            'logo_kanan' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:5120'],

            // Atribut surat
            'nomor_surat'   => ['required', 'string', 'max:191'],
            'tanggal_surat' => ['required', 'date'],

            // Data warga
            'nama_warga'   => ['required', 'string', 'max:191'],
            'tempat_lahir' => ['required', 'string', 'max:191'],
            'tanggal_lahir'=> ['required', 'date'],
            'pekerjaan'    => ['required', 'string', 'max:191'],
            'alamat_warga' => ['nullable', 'string'],
            'rt'           => ['nullable', 'string', 'max:10'],
            'rw'           => ['nullable', 'string', 'max:10'],
            'dusun'        => ['nullable', 'string', 'max:191'],

            // Isi
            'keterangan' => ['nullable', 'string'],

            // Stempel watermark
            'stempel' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:5120'],

            // Penandatangan (JSON dari frontend, dikirim sebagai JSON string)
            'penandatangan_list' => ['nullable'],
        ];
    }
}
