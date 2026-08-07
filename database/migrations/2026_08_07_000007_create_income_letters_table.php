<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('income_letters', function (Blueprint $table) {
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

            // Atribut Surat
            $table->string('nomor_surat', 191);
            $table->date('tanggal_surat');

            // Data Warga
            $table->string('nama_warga', 191);
            $table->string('nik', 20)->nullable();
            $table->string('tempat_lahir', 191);
            $table->date('tanggal_lahir');
            $table->string('jenis_kelamin', 20); // Perempuan / Laki – Laki
            $table->string('pekerjaan', 191);
            $table->string('agama', 50)->default('Islam');
            $table->text('alamat_warga')->nullable();
            $table->string('rt', 10)->nullable();
            $table->string('rw', 10)->nullable();
            $table->string('dusun', 191)->nullable();

            // Detail Penghasilan
            $table->string('nominal_penghasilan', 100)->nullable(); // cth: Rp 1.500.000,- / bulan (opsional)
            $table->text('keterangan_penghasilan')->nullable();     // Teks paragraf rincian penghasilan
            $table->text('kalimat_penutup')->nullable();

            // Penandatangan (Tunggal Kanan)
            $table->string('label_atasan', 191)->nullable();       // cth: a.n Kepala Desa ...
            $table->string('jabatan_penandatangan', 191)->nullable(); // Kepala Desa Sirnaputra
            $table->string('nama_penandatangan', 191)->nullable();    // Suhendar
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
        Schema::dropIfExists('income_letters');
    }
};
