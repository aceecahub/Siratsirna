<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('heir_letters', function (Blueprint $table) {
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

            // Pejabat Desa
            $table->string('nama_kades', 191)->default('Suhendar');
            $table->string('jabatan_kades', 191)->default('Kepala Desa Sirnaputra');

            // Data Almarhum / Almarhumah
            $table->string('nama_almarhum', 191);
            $table->string('nik_almarhum', 20)->nullable();
            $table->string('jenis_kelamin_almarhum', 20)->default('Laki-Laki');
            $table->string('tempat_lahir_almarhum', 191)->nullable();
            $table->date('tanggal_lahir_almarhum')->nullable();
            $table->string('agama_almarhum', 50)->default('Islam');
            $table->text('alamat_almarhum')->nullable();
            $table->string('rt_almarhum', 10)->nullable();
            $table->string('rw_almarhum', 10)->nullable();
            $table->string('dusun_almarhum', 191)->nullable();

            // Info Meninggal
            $table->string('hari_meninggal', 50)->nullable();       // cth: selasa
            $table->date('tanggal_meninggal')->nullable();         // 27 Januari 2026
            $table->string('tempat_meninggal', 191)->default('Rumah');
            $table->string('sebab_meninggal', 191)->default('sakit');
            $table->string('tpu_pemakaman', 191)->nullable();       // cth: TPU Gunung Tanjung Desa Sirnaputra

            // Dynamic Lists (JSON)
            // ahli_waris_list: [{ nama, nik, tempat_lahir, tanggal_lahir, pekerjaan, alamat, hubungan }]
            $table->json('ahli_waris_list');
            // saksi_list: [{ nama }]
            $table->json('saksi_list')->nullable();

            // Yang Menyatakan (Ahli Waris / Pemohon)
            $table->string('nama_yang_menyatakan', 191)->nullable();  // cth: Neni
            $table->string('label_yang_menyatakan', 191)->default('Ahli waris');

            // Pengesahan Camat (Kiri)
            $table->string('no_reg_camat', 191)->nullable();
            $table->string('jabatan_camat', 191)->default('Camat Cigalontang');
            $table->string('nama_camat', 191)->nullable();

            // TTD & Stempel Kades (Kanan)
            $table->string('tanda_tangan_path', 191)->nullable();
            $table->string('stempel_path', 191)->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('heir_letters');
    }
};
