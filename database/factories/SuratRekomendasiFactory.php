<?php

namespace Database\Factories;

use App\Models\Departemen;
use App\Models\Petugas;
use App\Models\SuratRekomendasi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SuratRekomendasi>
 */
class SuratRekomendasiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nomor_surat' => fake()->unique()->numerify('###/IT-SGG/X/2026'),
            'petugas_id' => Petugas::factory(),
            'departemen_id' => Departemen::factory(),
            // Perusahaan mengikuti departemen agar pasangan selalu valid
            'perusahaan_id' => fn (array $attributes) => Departemen::find($attributes['departemen_id'])->perusahaan_id,
            'nama_pengguna' => fake()->name(),
            'jenis_perangkat' => 'Laptop',
            'merk_perangkat' => fake()->randomElement(['Lenovo', 'Asus', 'HP']),
            'model_perangkat' => fake()->bothify('??-####'),
            'spesifikasi_perangkat' => 'Intel Core i5, RAM 8 GB, SSD 512 GB',
            'keterangan_rekomendasi' => fake()->sentence(),
        ];
    }
}
