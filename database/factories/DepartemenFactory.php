<?php

namespace Database\Factories;

use App\Models\Departemen;
use App\Models\Perusahaan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Departemen>
 */
class DepartemenFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'perusahaan_id' => Perusahaan::factory(),
            'nama' => 'Instalasi '.ucfirst(fake()->unique()->word()),
        ];
    }
}
