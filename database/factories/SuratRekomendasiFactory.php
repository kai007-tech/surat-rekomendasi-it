<?php

namespace Database\Factories;

use App\Enums\DepartemenEnum;
use App\Enums\PerusahaanEnum;
use App\Enums\PetugasEnum;
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
            'nama_petugas' => fake()->randomElement(PetugasEnum::cases()),
            'nama_perusahaan' => PerusahaanEnum::RSU_SYIFA_MEDIKA_BANJARBARU,
            'nama_departemen' => DepartemenEnum::INSTALASI_FARMASI,
            'nama_pengguna' => fake()->name(),
            'jenis_perangkat' => 'Laptop',
            'merk_perangkat' => fake()->randomElement(['Lenovo', 'Asus', 'HP']),
            'model_perangkat' => fake()->bothify('??-####'),
            'spesifikasi_perangkat' => 'Intel Core i5, RAM 8 GB, SSD 512 GB',
            'keterangan_rekomendasi' => fake()->sentence(),
        ];
    }
}
