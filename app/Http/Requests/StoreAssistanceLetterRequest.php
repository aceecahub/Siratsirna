<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssistanceLetterRequest extends FormRequest
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

            // Header
            'nomor_surat'    => ['required', 'string', 'max:191'],
            'tanggal_surat'  => ['required', 'date'],
            'sifat_surat'    => ['nullable', 'string', 'max:100'],
            'lampiran_surat' => ['nullable', 'string', 'max:100'],
            'perihal_surat'  => ['nullable', 'string', 'max:191'],

            // Tujuan
            'tujuan_nama' => ['nullable', 'string', 'max:191'],
            'tujuan_cq'   => ['nullable', 'string', 'max:191'],
            'tujuan_di'   => ['nullable', 'string', 'max:191'],
            'nama_tujuan' => ['nullable', 'string', 'max:191'],
            'tujuan_surat'=> ['nullable', 'string', 'max:191'],

            // Identitas Pasien
            'no_kk'              => ['nullable', 'string', 'max:30'],
            'nik'                => ['nullable', 'string', 'max:30'],
            'nik_pasien'         => ['nullable', 'string', 'max:30'],
            'nama_pasien'        => ['required', 'string', 'max:191'],
            'jenis_kelamin'      => ['nullable', 'string', 'max:20'],
            'jenis_kelamin_pasien'=>['nullable', 'string', 'max:20'],
            'tempat_lahir'       => ['nullable', 'string', 'max:191'],
            'tanggal_lahir'      => ['nullable', 'date'],
            'pekerjaan'          => ['nullable', 'string', 'max:191'],
            'pekerjaan_pasien'   => ['nullable', 'string', 'max:191'],
            'alamat_pasien'      => ['nullable', 'string'],
            'rt'                 => ['nullable', 'string', 'max:10'],
            'rw'                 => ['nullable', 'string', 'max:10'],
            'dusun'              => ['nullable', 'string', 'max:191'],
            'id_dtks'            => ['nullable', 'string', 'max:100'],

            // Isi Permohonan & Lampiran
            'teks_permohonan' => ['nullable', 'string'],
            'lampiran_list'   => ['nullable'],
            'keterangan'      => ['nullable', 'string'],
            'kalimat_penutup' => ['nullable', 'string'],

            // TTD 3 Kolom
            'jabatan_ttd_kiri'   => ['nullable', 'string', 'max:191'],
            'nama_ttd_kiri'      => ['nullable', 'string', 'max:191'],

            'label_ttd_tengah'   => ['nullable', 'string', 'max:191'],
            'jabatan_ttd_tengah' => ['nullable', 'string', 'max:191'],
            'nama_ttd_tengah'    => ['nullable', 'string', 'max:191'],

            'label_ttd_kanan'   => ['nullable', 'string', 'max:191'],
            'jabatan_ttd_kanan' => ['nullable', 'string', 'max:191'],
            'nama_ttd_kanan'    => ['nullable', 'string', 'max:191'],

            'tanda_tangan' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'stempel'      => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
        ];
    }
}
