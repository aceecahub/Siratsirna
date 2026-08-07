<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class HeirLetter extends Model
{
    protected $fillable = [
        'kabupaten', 'kecamatan', 'nama_desa', 'alamat', 'kode_pos', 'kontak', 'email_desa',
        'logo_kiri_path', 'logo_kanan_path',
        'nomor_surat', 'tanggal_surat',
        'nama_kades', 'jabatan_kades',
        'nama_almarhum', 'nik_almarhum', 'jenis_kelamin_almarhum',
        'tempat_lahir_almarhum', 'tanggal_lahir_almarhum', 'agama_almarhum',
        'alamat_almarhum', 'rt_almarhum', 'rw_almarhum', 'dusun_almarhum',
        'hari_meninggal', 'tanggal_meninggal', 'tempat_meninggal', 'sebab_meninggal', 'tpu_pemakaman',
        'ahli_waris_list', 'saksi_list',
        'nama_yang_menyatakan', 'label_yang_menyatakan',
        'no_reg_camat', 'jabatan_camat', 'nama_camat',
        'tanda_tangan_path', 'stempel_path',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_surat'         => 'date',
            'tanggal_lahir_almarhum'=> 'date',
            'tanggal_meninggal'     => 'date',
            'ahli_waris_list'       => 'array',
            'saksi_list'            => 'array',
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
