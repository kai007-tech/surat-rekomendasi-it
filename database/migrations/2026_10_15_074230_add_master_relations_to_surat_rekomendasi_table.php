<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Master data pindah dari enum ke tabel. Kolom nama_* di surat tetap disimpan sebagai
     * salinan (snapshot) agar surat lama tidak berubah saat data master diubah/dihapus.
     */
    public function up(): void
    {
        Schema::table('surat_rekomendasi', function (Blueprint $table) {
            $table->foreignId('petugas_id')->nullable()->after('nomor_surat')->constrained('petugas')->nullOnDelete();
            $table->foreignId('perusahaan_id')->nullable()->after('jabatan_petugas')->constrained('perusahaan')->nullOnDelete();
            $table->foreignId('departemen_id')->nullable()->after('nama_perusahaan')->constrained('departemen')->nullOnDelete();
        });

        $now = now();

        // Data awal dari enum lama
        $petugas = [
            'Muhammad Ferdy Priamjaya' => 'IT Network PT. Syifa Global Group',
            'Muhammad Jaka Saadillah' => 'IT Support PT. Syifa Global Group',
        ];
        $perusahaan = ['PT. Syifa Global Group', 'RSU Syifa Medika Banjarbaru'];

        // Ditambah nilai yang sudah dipakai surat yang tersimpan
        $surat = DB::table('surat_rekomendasi')->get();
        foreach ($surat as $row) {
            $petugas[$row->nama_petugas] ??= $row->jabatan_petugas;
            $perusahaan[] = $row->nama_perusahaan;
        }

        foreach ($petugas as $nama => $jabatan) {
            DB::table('petugas')->insert(['nama' => $nama, 'jabatan' => $jabatan, 'created_at' => $now, 'updated_at' => $now]);
        }
        foreach (array_unique($perusahaan) as $nama) {
            DB::table('perusahaan')->insert(['nama' => $nama, 'created_at' => $now, 'updated_at' => $now]);
        }

        $petugasIds = DB::table('petugas')->pluck('id', 'nama');
        $perusahaanIds = DB::table('perusahaan')->pluck('id', 'nama');

        foreach ($surat as $row) {
            $perusahaanId = $perusahaanIds[$row->nama_perusahaan];
            $departemenId = DB::table('departemen')
                ->where('perusahaan_id', $perusahaanId)
                ->where('nama', $row->nama_departemen)
                ->value('id')
                ?? DB::table('departemen')->insertGetId([
                    'perusahaan_id' => $perusahaanId,
                    'nama' => $row->nama_departemen,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

            DB::table('surat_rekomendasi')->where('id', $row->id)->update([
                'petugas_id' => $petugasIds[$row->nama_petugas],
                'perusahaan_id' => $perusahaanId,
                'departemen_id' => $departemenId,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('surat_rekomendasi', function (Blueprint $table) {
            $table->dropConstrainedForeignId('petugas_id');
            $table->dropConstrainedForeignId('perusahaan_id');
            $table->dropConstrainedForeignId('departemen_id');
        });

        DB::table('departemen')->delete();
        DB::table('perusahaan')->delete();
        DB::table('petugas')->delete();
    }
};
