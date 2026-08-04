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
        Schema::create('incoming_mails', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_agenda', 50)->unique();
            $table->string('nomor_surat_asal', 191)->nullable();
            $table->date('tanggal_surat');
            $table->date('tanggal_diterima');
            $table->string('pengirim', 191);
            $table->string('perihal', 191);
            $table->string('kategori', 50)->index();
            $table->string('file_name', 191);
            $table->string('file_path', 191);
            $table->string('file_extension', 10);
            $table->unsignedBigInteger('file_size');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['tanggal_surat', 'tanggal_diterima']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incoming_mails');
    }
};
