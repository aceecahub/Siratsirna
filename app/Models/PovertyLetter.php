<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[UseFactory([
    'kabupaten', 'kecamatan', 'nama_desa', 'alamat', 'kode_pos', 'kontak', 'email_desa',
    'logo_kiri_path', 'logo_kanan_path',
    'nomor_surat', 'tanggal_surat',
    'nama_warga', 'tempat_lahir', 'tanggal_lahir', 'pekerjaan',
    'alamat_warga', 'rt', 'rw', 'dusun',
    'keterangan',
    'penandatangan_list',
    'stempel_path',
    'created_by',
])]
class PovertyLetter extends Model
{
    protected $fillable = [
        'kabupaten', 'kecamatan', 'nama_desa', 'alamat', 'kode_pos', 'kontak', 'email_desa',
        'logo_kiri_path', 'logo_kanan_path',
        'nomor_surat', 'tanggal_surat',
        'nama_warga', 'tempat_lahir', 'tanggal_lahir', 'pekerjaan',
        'alamat_warga', 'rt', 'rw', 'dusun',
        'keterangan',
        'penandatangan_list',
        'stempel_path',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_surat'      => 'date',
            'tanggal_lahir'      => 'date',
            'penandatangan_list' => 'array',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Accessors ─────────────────────────────────────────────────
    public function getLogoKiriUrlAttribute(): ?string
    {
        return $this->logo_kiri_path
            ? Storage::disk('public')->url($this->logo_kiri_path)
            : null;
    }

    public function getLogoKananUrlAttribute(): ?string
    {
        return $this->logo_kanan_path
            ? Storage::disk('public')->url($this->logo_kanan_path)
            : null;
    }

    public function getStempelUrlAttribute(): ?string
    {
        return $this->stempel_path
            ? Storage::disk('public')->url($this->stempel_path)
            : null;
    }
}
