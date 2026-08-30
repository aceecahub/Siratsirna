<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDomicileLetterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Kop Surat — semua nullable karena bersifat statis (diisi default di controller)
            'kabupaten'  => ['nullable', 'string', 'max:191'],
            'kecamatan'  => ['nullable', 'string', 'max:191'],
            'nama_desa'  => ['nullable', 'string', 'max:191'],
            'alamat'     => ['nullable', 'string'],
            'kode_pos'   => ['nullable', 'string', 'max:10'],
            'kontak'     => ['nullable', 'string', 'max:191'],
            'email_desa' => ['nullable', 'email', 'max:191'],
            'logo_kiri'  => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'logo_kanan' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],

            // Atribut surat
            'nomor_surat'   => ['required', 'string', 'max:191'],
            'tanggal_surat' => ['required', 'date'],

            // Data warga
            'nik'            => ['required', 'string', 'max:50'],
            'nama_warga'     => ['required', 'string', 'max:191'],
            'tempat_lahir'   => ['required', 'string', 'max:191'],
            'tanggal_lahir'  => ['required', 'date'],
            'jenis_kelamin'  => ['required', 'string', 'max:20'],
            'warga_negara'   => ['nullable', 'string', 'max:50'],
            'agama'          => ['required', 'string', 'max:50'],
            'pekerjaan'      => ['required', 'string', 'max:191'],
            'alamat_warga'   => ['nullable', 'string'],
            'rt'             => ['nullable', 'string', 'max:10'],
            'rw'             => ['nullable', 'string', 'max:10'],
            'dusun'          => ['nullable', 'string', 'max:191'],

            // Keterangan
            'keterangan_domisili' => ['nullable', 'string'],
            'catatan_ektp'        => ['nullable', 'string'],

            // Tanda tangan
            'jabatan_penandatangan' => ['nullable', 'string', 'max:191'],
            'nama_penandatangan'    => ['nullable', 'string', 'max:191'],
            'nip_niapd'             => ['nullable', 'string', 'max:191'],
            'tanda_tangan'          => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'stempel'               => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
        ];
    }
}
