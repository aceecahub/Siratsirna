<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loss_letters', function (Blueprint $table) {
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

            // Data Warga / Pemilik
            $table->string('nama_warga', 191);
            $table->string('jenis_kelamin', 20); // Laki – Laki / Perempuan
            $table->text('alamat_warga')->nullable();
            $table->string('rt', 10)->nullable();
            $table->string('rw', 10)->nullable();
            $table->string('dusun', 191)->nullable();
            $table->string('desa_warga', 191)->nullable();
            $table->string('kecamatan_warga', 191)->nullable();
            $table->string('kabupaten_warga', 191)->nullable();

            // Detail Kehilangan (Barang/Dokumen yang Hilang & Keterangan Rinci)
            $table->string('barang_hilang', 191)->nullable(); // cth: SPPT Pajak Bumi & Bangunan / Kartu Keluarga / dll
            $table->text('keterangan_kehilangan')->nullable(); // Teks paragraf rincian barang/lokasi/nop/luas
            $table->text('kalimat_penutup')->nullable();

            // Penandatangan (Tunggal Kanan)
            $table->string('label_atasan', 191)->nullable();       // cth: a.n Kepala Desa ...
            $table->string('jabatan_penandatangan', 191)->nullable(); // Kepala Desa Sirnaputra
            $table->string('nama_penandatangan', 191)->nullable();    // SUHENDAR
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
        Schema::dropIfExists('loss_letters');
    }
};
