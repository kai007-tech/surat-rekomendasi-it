<?php

namespace App\Models;

use Database\Factories\SuratRekomendasiFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SuratRekomendasi extends Model
{
    /** @use HasFactory<SuratRekomendasiFactory> */
    use HasFactory;

    protected $table = 'surat_rekomendasi';

    // nama_petugas, jabatan_petugas, nama_perusahaan, nama_departemen diisi otomatis dari master (lihat booted)
    protected $fillable = [
        'nomor_surat',
        'petugas_id',
        'perusahaan_id',
        'departemen_id',
        'nama_pengguna',
        'jenis_perangkat',
        'merk_perangkat',
        'model_perangkat',
        'spesifikasi_perangkat',
        'keterangan_rekomendasi',
        'dokumentasi_perangkat',
        'tanggal_surat',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
    ];

    protected static function booted(): void
    {
        // Salin nama/jabatan dari master saat relasi dipilih/diganti, agar surat lama
        // tetap tercetak sama walau data master kemudian diubah atau dihapus
        static::saving(function (SuratRekomendasi $surat) {
            if ($surat->isDirty('petugas_id') && $surat->petugas) {
                $surat->nama_petugas = $surat->petugas->nama;
                $surat->jabatan_petugas = $surat->petugas->jabatan;
            }

            if ($surat->isDirty('perusahaan_id') && $surat->perusahaan) {
                $surat->nama_perusahaan = $surat->perusahaan->nama;
            }

            if ($surat->isDirty('departemen_id') && $surat->departemen) {
                $surat->nama_departemen = $surat->departemen->nama;
            }

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

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(Petugas::class);
    }

    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class);
    }

    // Nama file PDF aman (nomor surat biasanya mengandung "/")
    public function pdfFileName(): string
    {
        return 'surat-rekomendasi-'.Str::slug(str_replace('/', ' ', $this->nomor_surat)).'.pdf';
    }
}
