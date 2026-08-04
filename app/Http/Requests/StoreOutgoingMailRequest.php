<?php

namespace App\Http\Requests;

use App\Models\OutgoingMail;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOutgoingMailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kabupaten' => ['required', 'string', 'max:191'],
            'kecamatan' => ['required', 'string', 'max:191'],
            'nama_desa' => ['required', 'string', 'max:191'],
            'alamat' => ['required', 'string'],
            'kode_pos' => ['nullable', 'string', 'max:10'],
            'kontak' => ['nullable', 'string', 'max:191'],
            'email_desa' => ['nullable', 'email', 'max:191'],
            'logo_kiri' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:5120'],
            'logo_kanan' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:5120'],

            'nomor_surat' => ['required', 'string', 'max:191'],
            'sifat' => ['required', 'string', Rule::in(OutgoingMail::SIFAT)],
            'lampiran' => ['nullable', 'string', 'max:191'],
            'perihal' => ['required', 'string', 'max:191'],
            'tanggal_surat' => ['required', 'date'],
            'tujuan' => ['required', 'string', 'max:191'],

            'pembuka' => ['required', 'string'],
            'isi_surat' => ['required', 'string'],
            'penutup' => ['required', 'string'],

            'jabatan_penandatangan' => ['nullable', 'string', 'max:191'],
            'nama_penandatangan' => ['nullable', 'string', 'max:191'],
            'nip_niapd' => ['nullable', 'string', 'max:191'],
            'tanda_tangan' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:5120'],
            'stempel' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:5120'],

            'penandatangan_list' => ['nullable'],
        ];
    }
}
