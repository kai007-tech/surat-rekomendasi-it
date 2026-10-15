<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_rekomendasi', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->unique();
            $table->string('nama_petugas');
            $table->string('jabatan_petugas');
            $table->string('nama_perusahaan');
            $table->string('nama_departemen');
            $table->string('nama_pengguna');
            $table->string('jenis_perangkat');
            $table->string('merk_perangkat');
            $table->string('model_perangkat')->default('-');
            $table->text('spesifikasi_perangkat')->nullable();
            $table->text('keterangan_rekomendasi');
            $table->string('dokumentasi_perangkat')->nullable();
            // Diisi otomatis dengan tanggal pembuatan (model SuratRekomendasi)
            $table->date('tanggal_surat');
            $table->string('jabatan_penandatangan')->default('IT Supervisor PT. Syifa Global Group');
            $table->string('nama_penandatangan')->default('Lutfi Salisa Setiawati, S.Kom');
            $table->string('nip_penandatangan')->default('01122016017');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_rekomendasi');
    }
};
