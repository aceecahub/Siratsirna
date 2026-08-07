<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ktp_letters', function (Blueprint $table) {
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

            // Data Warga
            $table->string('nik', 20);
            $table->string('nama_warga', 191);
            $table->string('tempat_lahir', 191);
            $table->date('tanggal_lahir');
            $table->string('warga_negara', 50)->default('Indonesia');
            $table->string('jenis_kelamin', 20); // Laki - Laki / Perempuan
            $table->string('agama', 50);
            $table->string('status_perkawinan', 50); // Belum Kawin / Kawin / Cerai Hidup / Cerai Mati
            $table->string('pekerjaan', 191);
            $table->text('alamat_warga')->nullable();
            $table->string('rt', 10)->nullable();
            $table->string('rw', 10)->nullable();
            $table->string('dusun', 191)->nullable();

            // Isi / Teks tambahan
            $table->text('keterangan_tambahan')->nullable();
            $table->text('kalimat_penutup')->nullable();

            // Penandatangan (Tunggal Kanan)
            $table->string('label_atasan', 191)->nullable(); // cth: a.n Kepala Desa
            $table->string('jabatan_penandatangan', 191)->nullable(); // Kepala Desa Sirnaputra
            $table->string('nama_penandatangan', 191)->nullable();    // Subhendar
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
        Schema::dropIfExists('ktp_letters');
    }
};
