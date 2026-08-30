<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class LossLetter extends Model
{
    protected $fillable = [
        'kabupaten', 'kecamatan', 'nama_desa', 'alamat', 'kode_pos', 'kontak', 'email_desa',
        'logo_kiri_path', 'logo_kanan_path',
        'nomor_surat', 'tanggal_surat',
        'nik', 'nama_warga', 'tempat_lahir', 'tanggal_lahir', 'agama', 'pekerjaan',
        'jenis_kelamin',
        'alamat_warga', 'rt', 'rw', 'dusun', 'desa_warga', 'kecamatan_warga', 'kabupaten_warga',
        'barang_hilang', 'lokasi_kehilangan', 'waktu_kehilangan',
        'keterangan_kehilangan', 'keterangan', 'kalimat_penutup',
        'label_atasan', 'jabatan_penandatangan', 'nama_penandatangan', 'nip_niapd',
        'tanda_tangan_path', 'stempel_path',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_surat' => 'date',
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
