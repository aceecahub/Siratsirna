<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('outgoing_mails', function (Blueprint $table) {
            $table->id();

            // Kop Surat (header)
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
            $table->string('sifat', 20)->default('Biasa');
            $table->string('lampiran', 191)->nullable();
            $table->string('perihal', 191);
            $table->date('tanggal_surat');
            $table->text('tujuan');

            // Isi surat
            $table->text('pembuka');
            $table->longText('isi_surat');
            $table->text('penutup');

            // Bagian tanda tangan
            $table->string('jabatan_penandatangan', 191);
            $table->string('nama_penandatangan', 191);
            $table->string('nip_niapd', 191)->nullable();
            $table->string('tanda_tangan_path', 191)->nullable();
            $table->string('stempel_path', 191)->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outgoing_mails');
    }
};
