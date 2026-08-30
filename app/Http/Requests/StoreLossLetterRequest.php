<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLossLetterRequest extends FormRequest
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

            // Data Warga
            'nik'             => ['nullable', 'string', 'max:50'],
            'nama_warga'      => ['required', 'string', 'max:191'],
            'tempat_lahir'    => ['nullable', 'string', 'max:191'],
            'tanggal_lahir'   => ['nullable', 'date'],
            'agama'           => ['nullable', 'string', 'max:50'],
            'pekerjaan'       => ['nullable', 'string', 'max:191'],
            'jenis_kelamin'   => ['nullable', 'string', 'max:20'],
            'alamat_warga'    => ['nullable', 'string'],
            'rt'              => ['nullable', 'string', 'max:10'],
            'rw'              => ['nullable', 'string', 'max:10'],
            'dusun'           => ['nullable', 'string', 'max:191'],
            'desa_warga'      => ['nullable', 'string', 'max:191'],
            'kecamatan_warga' => ['nullable', 'string', 'max:191'],
            'kabupaten_warga' => ['nullable', 'string', 'max:191'],

            // Detail Kehilangan
            'barang_hilang'         => ['nullable', 'string', 'max:191'],
            'lokasi_kehilangan'     => ['nullable', 'string', 'max:191'],
            'waktu_kehilangan'      => ['nullable', 'string', 'max:191'],
            'keterangan_kehilangan' => ['nullable', 'string'],
            'keterangan'            => ['nullable', 'string'],
            'kalimat_penutup'       => ['nullable', 'string'],

            // TTD
            'label_atasan'          => ['nullable', 'string', 'max:191'],
            'jabatan_penandatangan' => ['nullable', 'string', 'max:191'],
            'nama_penandatangan'    => ['nullable', 'string', 'max:191'],
            'nip_niapd'             => ['nullable', 'string', 'max:191'],
            'tanda_tangan'          => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'stempel'               => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
        ];
    }
}
