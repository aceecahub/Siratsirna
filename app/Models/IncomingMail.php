<?php

namespace App\Models;

use Database\Factories\IncomingMailFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'nomor_agenda',
    'nomor_surat_asal',
    'tanggal_surat',
    'tanggal_diterima',
    'pengirim',
    'perihal',
    'kategori',
    'file_name',
    'file_path',
    'file_extension',
    'file_size',
    'created_by',
])]
class IncomingMail extends Model
{
    /** @use HasFactory<IncomingMailFactory> */
    use HasFactory;

    public const CATEGORIES = [
        'Surat Dinas',
        'Undangan',
        'Edaran',
        'Permohonan',
        'Pengumuman',
        'Laporan',
        'Rekomendasi',
        'Keterangan',
        'Lainnya',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_surat' => 'date',
            'tanggal_diterima' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFileUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

    public function isPdf(): bool
    {
        return $this->file_extension === 'pdf';
    }
}
