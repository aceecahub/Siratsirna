<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('poverty_letters', function (Blueprint $table) {
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

            // Data warga (lebih sederhana dari domisili - tanpa NIK, agama, warga negara, jenis kelamin)
            $table->string('nama_warga', 191);
            $table->string('tempat_lahir', 191);
            $table->date('tanggal_lahir');
            $table->string('pekerjaan', 191);
            $table->text('alamat_warga');
            $table->string('rt', 10)->nullable();
            $table->string('rw', 10)->nullable();
            $table->string('dusun', 191)->nullable();

            // Isi surat (nullable → ada teks default)
            $table->text('keterangan')->nullable();

            // Penandatangan (JSON array: kiri, kanan, mengetahui)
            $table->json('penandatangan_list')->nullable();

            // Stempel watermark
            $table->string('stempel_path', 191)->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poverty_letters');
    }
};
