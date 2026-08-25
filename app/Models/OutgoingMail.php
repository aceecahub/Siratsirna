<?php

namespace App\Models;

use Database\Factories\OutgoingMailFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class OutgoingMail extends Model
{
    /** @use HasFactory<OutgoingMailFactory> */
    use HasFactory;

    protected $fillable = [
        'kabupaten',
        'kecamatan',
        'nama_desa',
        'alamat',
        'kode_pos',
        'kontak',
        'email_desa',
        'logo_kiri_path',
        'logo_kanan_path',
        'nomor_surat',
        'sifat',
        'lampiran',
        'perihal',
        'tanggal_surat',
        'tujuan',
        'pembuka',
        'isi_surat',
        'penutup',
        'jabatan_penandatangan',
        'nama_penandatangan',
        'nip_niapd',
        'tanda_tangan_path',
        'stempel_path',
        'penandatangan_list',
        'created_by',
    ];

    public const SIFAT = ['Biasa', 'Penting', 'Rahasia'];

    protected function casts(): array
    {
        return [
            'tanggal_surat' => 'date',
            'penandatangan_list' => 'array',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getLogoKiriUrlAttribute(): ?string
    {
        return $this->logo_kiri_path ? Storage::disk('public')->url($this->logo_kiri_path) : null;
    }

    public function getLogoKananUrlAttribute(): ?string
    {
        return $this->logo_kanan_path ? Storage::disk('public')->url($this->logo_kanan_path) : null;
    }

    public function getTandaTanganUrlAttribute(): ?string
    {
        return $this->tanda_tangan_path ? Storage::disk('public')->url($this->tanda_tangan_path) : null;
    }

    public function getStempelUrlAttribute(): ?string
    {
        return $this->stempel_path ? Storage::disk('public')->url($this->stempel_path) : null;
    }
}
