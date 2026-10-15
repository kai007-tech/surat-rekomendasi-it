<?php

namespace App\Models;

use App\Enums\DepartemenEnum;
use App\Enums\PerusahaanEnum;
use App\Enums\PetugasEnum;
use Database\Factories\SuratRekomendasiFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SuratRekomendasi extends Model
{
    /** @use HasFactory<SuratRekomendasiFactory> */
    use HasFactory;

    protected $table = 'surat_rekomendasi';

    // Data penandatangan & tanggal surat tidak diisi lewat form (default migration / otomatis)
    protected $fillable = [
        'nomor_surat',
        'nama_petugas',
        'nama_perusahaan',
        'nama_departemen',
        'nama_pengguna',
        'jenis_perangkat',
        'merk_perangkat',
        'model_perangkat',
        'spesifikasi_perangkat',
        'keterangan_rekomendasi',
        'dokumentasi_perangkat',
    ];

    protected $casts = [
        'nama_petugas' => PetugasEnum::class,
        'nama_perusahaan' => PerusahaanEnum::class,
        'nama_departemen' => DepartemenEnum::class,
        'tanggal_surat' => 'date',
    ];

    protected static function booted(): void
    {
        // Jabatan selalu dari enum (bukan dari input form); tanggal surat = tanggal pembuatan
        static::saving(function (SuratRekomendasi $surat) {
            $surat->jabatan_petugas = $surat->nama_petugas->jabatan();
            $surat->model_perangkat = filled($surat->model_perangkat) ? $surat->model_perangkat : '-';
            $surat->tanggal_surat ??= today();
        });

        // Hapus file dokumentasi lama saat diganti/dikosongkan atau saat surat dihapus
        static::updated(function (SuratRekomendasi $surat) {
            $old = $surat->getOriginal('dokumentasi_perangkat');

            if ($surat->wasChanged('dokumentasi_perangkat') && $old) {
                Storage::disk('public')->delete($old);
            }
        });

        static::deleting(function (SuratRekomendasi $surat) {
            if ($surat->dokumentasi_perangkat) {
                Storage::disk('public')->delete($surat->dokumentasi_perangkat);
            }
        });
    }

    // Nama file PDF aman (nomor surat biasanya mengandung "/")
    public function pdfFileName(): string
    {
        return 'surat-rekomendasi-'.Str::slug(str_replace('/', ' ', $this->nomor_surat)).'.pdf';
    }
}
