<?php

namespace App\Http\Requests;

use App\Models\IncomingMail;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIncomingMailRequest extends FormRequest
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
            'file' => ['required', 'file', 'mimes:pdf,png,jpg,jpeg', 'max:5120'],
            'nomor_surat_asal' => ['nullable', 'string', 'max:191'],
            'tanggal_surat' => ['required', 'date'],
            'tanggal_diterima' => ['required', 'date'],
            'pengirim' => ['required', 'string', 'max:191'],
            'perihal' => ['required', 'string', 'max:191'],
            'kategori' => ['required', 'string', Rule::in(IncomingMail::CATEGORIES)],
        ];
    }
}
