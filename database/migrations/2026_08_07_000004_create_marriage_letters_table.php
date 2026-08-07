<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marriage_letters', function (Blueprint $table) {
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

            // Atribut surat
            $table->string('nomor_surat', 191);
            $table->date('tanggal_surat');

            // Data Pemohon (yang meminta surat)
            $table->string('nama_pemohon', 191);
            $table->string('tempat_lahir', 191);
            $table->date('tanggal_lahir');
            $table->string('pekerjaan', 191);
            $table->text('alamat_pemohon')->nullable();
            $table->string('rt', 10)->nullable();
            $table->string('rw', 10)->nullable();
            $table->string('dusun', 191)->nullable();

            // Info Pernikahan
            $table->string('jenis_nikah', 100)->default('siri');  // siri / resmi / dll
            $table->string('hari_nikah', 50)->nullable();          // Senin, Kamis, dst
            $table->date('tanggal_nikah');
            $table->text('keterangan_nikah')->nullable();          // Override teks paragraf

            // Data Pasangan
            $table->string('nama_pasangan', 191);
            $table->string('tempat_lahir_pasangan', 191)->nullable();
            $table->date('tanggal_lahir_pasangan')->nullable();
            $table->text('alamat_pasangan')->nullable();

            // Penutup (nullable → ada default)
            $table->text('kalimat_penutup')->nullable();

            // Tanda Tangan (tunggal kanan)
            $table->string('label_atasan', 191)->nullable();       // cth: a.n Kepala Desa Sirnaputra
            $table->string('jabatan_penandatangan', 191)->nullable();
            $table->string('nama_penandatangan', 191)->nullable();
            $table->string('nip_niapd', 191)->nullable();
            $table->string('tanda_tangan_path', 191)->nullable();
            $table->string('stempel_path', 191)->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marriage_letters');
    }
};
