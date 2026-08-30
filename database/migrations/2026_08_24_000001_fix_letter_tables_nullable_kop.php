<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Make kop-surat and non-essential fields nullable across all 8 letter tables.
 * Also adds missing columns needed by the Vue Create/Edit forms.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. domicile_letters ─────────────────────────────────────
        Schema::table('domicile_letters', function (Blueprint $table) {
            $table->string('kabupaten', 191)->nullable()->change();
            $table->string('kecamatan', 191)->nullable()->change();
            $table->string('nama_desa', 191)->nullable()->change();
            $table->text('alamat')->nullable()->change();
            $table->text('alamat_warga')->nullable()->change();
            $table->string('jabatan_penandatangan', 191)->nullable()->change();
            $table->string('nama_penandatangan', 191)->nullable()->change();
        });

        // ── 2. poverty_letters ──────────────────────────────────────
        Schema::table('poverty_letters', function (Blueprint $table) {
            $table->string('kabupaten', 191)->nullable()->change();
            $table->string('kecamatan', 191)->nullable()->change();
            $table->string('nama_desa', 191)->nullable()->change();
            $table->text('alamat')->nullable()->change();
            $table->text('alamat_warga')->nullable()->change();

            if (!Schema::hasColumn('poverty_letters', 'jabatan_penandatangan')) {
                $table->string('jabatan_penandatangan', 191)->nullable()->after('keterangan');
            }
            if (!Schema::hasColumn('poverty_letters', 'nama_penandatangan')) {
                $table->string('nama_penandatangan', 191)->nullable()->after('jabatan_penandatangan');
            }
            if (!Schema::hasColumn('poverty_letters', 'nip_niapd')) {
                $table->string('nip_niapd', 191)->nullable()->after('nama_penandatangan');
            }
            if (!Schema::hasColumn('poverty_letters', 'tanda_tangan_path')) {
                $table->string('tanda_tangan_path', 191)->nullable()->after('nip_niapd');
            }
            if (!Schema::hasColumn('poverty_letters', 'nik')) {
                $table->string('nik', 50)->nullable()->after('nama_warga');
            }
            if (!Schema::hasColumn('poverty_letters', 'agama')) {
                $table->string('agama', 50)->nullable()->after('nik');
            }
            if (!Schema::hasColumn('poverty_letters', 'jenis_kelamin')) {
                $table->string('jenis_kelamin', 20)->nullable()->after('agama');
            }
            if (!Schema::hasColumn('poverty_letters', 'warga_negara')) {
                $table->string('warga_negara', 50)->nullable()->after('jenis_kelamin');
            }
        });

        // ── 3. marriage_letters ─────────────────────────────────────
        Schema::table('marriage_letters', function (Blueprint $table) {
            $table->string('kabupaten', 191)->nullable()->change();
            $table->string('kecamatan', 191)->nullable()->change();
            $table->string('nama_desa', 191)->nullable()->change();
            $table->text('alamat')->nullable()->change();
        });

        // ── 4. ktp_letters ──────────────────────────────────────────
        Schema::table('ktp_letters', function (Blueprint $table) {
            $table->string('kabupaten', 191)->nullable()->change();
            $table->string('kecamatan', 191)->nullable()->change();
            $table->string('nama_desa', 191)->nullable()->change();
            $table->text('alamat')->nullable()->change();
        });

        // ── 5. loss_letters ─────────────────────────────────────────
        Schema::table('loss_letters', function (Blueprint $table) {
            $table->string('kabupaten', 191)->nullable()->change();
            $table->string('kecamatan', 191)->nullable()->change();
            $table->string('nama_desa', 191)->nullable()->change();
            $table->text('alamat')->nullable()->change();
            $table->string('jenis_kelamin', 20)->nullable()->change();

            if (!Schema::hasColumn('loss_letters', 'nik')) {
                $table->string('nik', 50)->nullable()->after('nama_warga');
            }
            if (!Schema::hasColumn('loss_letters', 'tempat_lahir')) {
                $table->string('tempat_lahir', 191)->nullable()->after('nik');
            }
            if (!Schema::hasColumn('loss_letters', 'tanggal_lahir')) {
                $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            }
            if (!Schema::hasColumn('loss_letters', 'agama')) {
                $table->string('agama', 50)->nullable()->after('tanggal_lahir');
            }
            if (!Schema::hasColumn('loss_letters', 'pekerjaan')) {
                $table->string('pekerjaan', 191)->nullable()->after('agama');
            }
            if (!Schema::hasColumn('loss_letters', 'lokasi_kehilangan')) {
                $table->string('lokasi_kehilangan', 191)->nullable()->after('barang_hilang');
            }
            if (!Schema::hasColumn('loss_letters', 'waktu_kehilangan')) {
                $table->string('waktu_kehilangan', 191)->nullable()->after('lokasi_kehilangan');
            }
        });

        // ── 6. income_letters ───────────────────────────────────────
        Schema::table('income_letters', function (Blueprint $table) {
            $table->string('kabupaten', 191)->nullable()->change();
            $table->string('kecamatan', 191)->nullable()->change();
            $table->string('nama_desa', 191)->nullable()->change();
            $table->text('alamat')->nullable()->change();
            $table->string('jenis_kelamin', 20)->nullable()->change();

            if (!Schema::hasColumn('income_letters', 'jumlah_penghasilan')) {
                $table->string('jumlah_penghasilan', 100)->nullable()->after('nominal_penghasilan');
            }
            if (!Schema::hasColumn('income_letters', 'terbilang_penghasilan')) {
                $table->string('terbilang_penghasilan', 255)->nullable()->after('jumlah_penghasilan');
            }
            if (!Schema::hasColumn('income_letters', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('terbilang_penghasilan');
            }
        });

        // ── 7. heir_letters ─────────────────────────────────────────
        Schema::table('heir_letters', function (Blueprint $table) {
            $table->string('kabupaten', 191)->nullable()->change();
            $table->string('kecamatan', 191)->nullable()->change();
            $table->string('nama_desa', 191)->nullable()->change();
            $table->text('alamat')->nullable()->change();

            if (!Schema::hasColumn('heir_letters', 'lokasi_meninggal')) {
                $table->string('lokasi_meninggal', 191)->nullable()->after('tanggal_meninggal');
            }
        });

        // ── 8. assistance_letters ───────────────────────────────────
        Schema::table('assistance_letters', function (Blueprint $table) {
            $table->string('kabupaten', 191)->nullable()->change();
            $table->string('kecamatan', 191)->nullable()->change();
            $table->string('nama_desa', 191)->nullable()->change();
            $table->text('alamat')->nullable()->change();

            if (!Schema::hasColumn('assistance_letters', 'nama_tujuan')) {
                $table->string('nama_tujuan', 191)->nullable()->after('tujuan_nama');
            }
            if (!Schema::hasColumn('assistance_letters', 'tujuan_surat')) {
                $table->string('tujuan_surat', 191)->nullable()->after('nama_tujuan');
            }
            if (!Schema::hasColumn('assistance_letters', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('teks_permohonan');
            }
            if (!Schema::hasColumn('assistance_letters', 'nik_pasien')) {
                $table->string('nik_pasien', 30)->nullable()->after('nik');
            }
            if (!Schema::hasColumn('assistance_letters', 'jenis_kelamin_pasien')) {
                $table->string('jenis_kelamin_pasien', 20)->nullable()->after('nik_pasien');
            }
            if (!Schema::hasColumn('assistance_letters', 'pekerjaan_pasien')) {
                $table->string('pekerjaan_pasien', 191)->nullable()->after('jenis_kelamin_pasien');
            }
        });

        // ── 9. outgoing_mails ───────────────────────────────────────
        Schema::table('outgoing_mails', function (Blueprint $table) {
            $table->string('kabupaten', 191)->nullable()->change();
            $table->string('kecamatan', 191)->nullable()->change();
            $table->string('nama_desa', 191)->nullable()->change();
            $table->text('alamat')->nullable()->change();
            $table->string('jabatan_penandatangan', 191)->nullable()->change();
            $table->string('nama_penandatangan', 191)->nullable()->change();
        });
    }

    public function down(): void
    {
    }
};
