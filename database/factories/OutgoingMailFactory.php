<?php

namespace Database\Factories;

use App\Models\OutgoingMail;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OutgoingMail>
 */
class OutgoingMailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $desa = fake()->words(2, true);
        $jabatan = 'Kepala Desa';

        return [
            'kabupaten' => fake()->city(),
            'kecamatan' => fake()->city(),
            'nama_desa' => $desa,
            'alamat' => fake()->address(),
            'kode_pos' => fake()->numerify('#####'),
            'kontak' => fake()->phoneNumber(),
            'email_desa' => fake()->safeEmail(),
            'logo_kiri_path' => null,
            'logo_kanan_path' => null,
            'nomor_surat' => fake()->numerify('###/PD/'.now()->year.'/##'),
            'sifat' => fake()->randomElement(OutgoingMail::SIFAT),
            'lampiran' => fake()->randomElement(['1 (satu) berkas', '-']),
            'perihal' => fake()->sentence(4),
            'tanggal_surat' => now()->toDateString(),
            'tujuan' => 'Yth. '.fake()->name(),
            'pembuka' => 'Yang bertanda tangan di bawah ini, '.$jabatan.' Desa '.$desa.', dengan ini menerangkan bahwa:',
            'isi_surat' => fake()->paragraphs(3, true),
            'penutup' => 'Demikian surat ini dibuat untuk dipergunakan sebagaimana mestinya. Atas perhatian dan kerja samanya, kami ucapkan terima kasih.',
            'jabatan_penandatangan' => $jabatan,
            'nama_penandatangan' => fake()->name(),
            'nip_niapd' => fake()->numerify('################'),
            'tanda_tangan_path' => null,
            'stempel_path' => null,
            'created_by' => User::factory(),
        ];
    }
}
