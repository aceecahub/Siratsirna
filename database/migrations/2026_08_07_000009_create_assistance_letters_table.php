<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assistance_letters', function (Blueprint $table) {
            $table->id();

            // Kop Surat
            $table->string('kabupaten', 191);
            $table->string('kecamatan', 191);
            $table->string('nama_desa', 191);
            $table->text('alamat');
            $table->string('kode_pos', 10)->nullable();
            $table->string('kontak', 191)->nullable();
            $table->string('email_desa', 191)->nullable();
            $table->string('logo_kiri_path', 191)->nullable();
            $table->string('logo_kanan_path', 191)->nullable();

            // Header Kiri & Kanan (Nomor, Tujuan)
            $table->string('nomor_surat', 191);
            $table->date('tanggal_surat');
            $table->string('sifat_surat', 100)->default('Penting');
            $table->string('lampiran_surat', 100)->default('1 Set');
            $table->string('perihal_surat', 191)->nullable();

            $table->string('tujuan_nama', 191)->default('Bupati tasikmalaya');
            $table->string('tujuan_cq', 191)->nullable()->default('Cq. Dinas Sosial PPKB,P3A');
            $table->string('tujuan_di', 191)->nullable()->default('Singaparna, Tasikmalaya');

            // Identitas Pasien / Pemohon
            $table->string('no_kk', 30)->nullable();
            $table->string('nik', 30)->nullable();
            $table->string('nama_pasien', 191);
            $table->string('jenis_kelamin', 20)->default('Perempuan');
            $table->string('tempat_lahir', 191);
            $table->date('tanggal_lahir');
            $table->string('pekerjaan', 191)->default('Belum/Tidak Bekerja');
            $table->text('alamat_pasien')->nullable();
            $table->string('rt', 10)->nullable();
            $table->string('rw', 10)->nullable();
            $table->string('dusun', 191)->nullable();
            $table->string('id_dtks', 100)->nullable()->default('-');

            // Isi Permohonan & Lampiran
            $table->text('teks_permohonan')->nullable();
            $table->json('lampiran_list')->nullable();
            $table->text('kalimat_penutup')->nullable();

            // TTD 3 Kolom
            $table->string('jabatan_ttd_kiri', 191)->nullable()->default('Pendamping PKH /TKSK Kec.Cigalontang');
            $table->string('nama_ttd_kiri', 191)->nullable();

            $table->string('label_ttd_tengah', 191)->nullable()->default('Mengetahui,');
            $table->string('jabatan_ttd_tengah', 191)->nullable()->default('Camat Cigalontang');
            $table->string('nama_ttd_tengah', 191)->nullable();

            $table->string('label_ttd_kanan', 191)->nullable();
            $table->string('jabatan_ttd_kanan', 191)->nullable()->default('Kepala Desa Sirnaputra');
            $table->string('nama_ttd_kanan', 191)->nullable()->default('Suhendar');

            $table->string('tanda_tangan_path', 191)->nullable();
            $table->string('stempel_path', 191)->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assistance_letters');
    }
};
