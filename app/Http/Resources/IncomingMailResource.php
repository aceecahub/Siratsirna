<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IncomingMailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nomor_agenda' => $this->nomor_agenda,
            'nomor_surat_asal' => $this->nomor_surat_asal,
            'tanggal_surat' => $this->tanggal_surat?->toDateString(),
            'tanggal_diterima' => $this->tanggal_diterima?->toDateString(),
            'pengirim' => $this->pengirim,
            'perihal' => $this->perihal,
            'kategori' => $this->kategori,
            'file_name' => $this->file_name,
            'file_extension' => $this->file_extension,
            'file_size' => $this->file_size,
            'file_size_formatted' => $this->file_size > 0
                ? number_format($this->file_size / 1024 / 1024, 2).' MB'
                : '0 MB',
            'file_url' => $this->file_url,
            'created_by' => $this->whenLoaded('creator', fn () => $this->creator?->name),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
