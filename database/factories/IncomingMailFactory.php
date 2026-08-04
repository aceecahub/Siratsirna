<?php

namespace Database\Factories;

use App\Models\IncomingMail;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IncomingMail>
 */
class IncomingMailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $kategori = fake()->randomElement(IncomingMail::CATEGORIES);

        return [
            'nomor_agenda' => 'ARS-MASUK/'.now()->year.'/'.fake()->unique()->numberBetween(1, 999),
            'nomor_surat_asal' => fake()->numerify('###/PEM/####/##'),
            'tanggal_surat' => fake()->dateTimeBetween('-1 year')->format('Y-m-d'),
            'tanggal_diterima' => fake()->dateTimeBetween('-1 year')->format('Y-m-d'),
            'pengirim' => fake()->company(),
            'perihal' => fake()->sentence(4),
            'kategori' => $kategori,
            'file_name' => 'surat-masuk-'.fake()->word().'.pdf',
            'file_path' => 'surat-masuk/placeholder-'.fake()->uuid().'.pdf',
            'file_extension' => 'pdf',
            'file_size' => fake()->numberBetween(100_000, 4_900_000),
            'created_by' => User::factory(),
        ];
    }
}
