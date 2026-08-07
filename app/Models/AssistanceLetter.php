<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class AssistanceLetter extends Model
{
    protected $fillable = [
        'kabupaten', 'kecamatan', 'nama_desa', 'alamat', 'kode_pos', 'kontak', 'email_desa',
        'logo_kiri_path', 'logo_kanan_path',
        'nomor_surat', 'tanggal_surat', 'sifat_surat', 'lampiran_surat', 'perihal_surat',
        'tujuan_nama', 'tujuan_cq', 'tujuan_di',
        'no_kk', 'nik', 'nama_pasien', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir',
        'pekerjaan', 'alamat_pasien', 'rt', 'rw', 'dusun', 'id_dtks',
        'teks_permohonan', 'lampiran_list', 'kalimat_penutup',
        'jabatan_ttd_kiri', 'nama_ttd_kiri',
        'label_ttd_tengah', 'jabatan_ttd_tengah', 'nama_ttd_tengah',
        'label_ttd_kanan', 'jabatan_ttd_kanan', 'nama_ttd_kanan',
        'tanda_tangan_path', 'stempel_path',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_surat' => 'date',
            'tanggal_lahir' => 'date',
            'lampiran_list' => 'array',
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
