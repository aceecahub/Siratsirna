<?php

namespace Tests\Feature;

use App\Models\OutgoingMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OutgoingMailTest extends TestCase
{
    use RefreshDatabase;

    private function payload(): array
    {
        return [
            'kabupaten' => 'Bandung',
            'kecamatan' => 'Soreang',
            'nama_desa' => 'Siratsirna',
            'alamat' => 'Jl. Raya Siratsirna No. 1',
            'kode_pos' => '40951',
            'kontak' => '0812-3456-7890',
            'email_desa' => 'desa@example.com',
            'nomor_surat' => '421.2/001/PD/2026',
            'sifat' => 'Biasa',
            'lampiran' => '-',
            'perihal' => 'Undangan Rapat',
            'tanggal_surat' => now()->toDateString(),
            'tujuan' => 'Yth. Ketua RT 01',
            'pembuka' => 'Dengan hormat, kami mengundang Bapak/Ibu untuk hadir pada:',
            'isi_surat' => "Hari, tanggal : Senin, 3 Agustus 2026\nWaktu : Pukul 09.00 WIB\nTempat : Aula Desa",
            'penutup' => 'Demikian undangan ini disampaikan. Atas perhatiannya diucapkan terima kasih.',
            'jabatan_penandatangan' => 'Kepala Desa',
            'nama_penandatangan' => 'H. Surya Permana',
            'nip_niapd' => '1.9650101 199003 1 002',
        ];
    }

    public function test_create_renders_generator_page_with_templates(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('surat-keluar.create'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('OutgoingMail/Create')
                ->has('templates'));
    }

    public function test_store_persists_letter_and_uploads_assets(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('surat-keluar.store'), [
            ...$this->payload(),
            'logo_kiri' => UploadedFile::fake()->image('lambang.png', 120, 120),
        ]);

        $response->assertRedirect(route('surat-keluar.show', OutgoingMail::first()));

        $this->assertDatabaseHas('outgoing_mails', [
            'nomor_surat' => '421.2/001/PD/2026',
            'perihal' => 'Undangan Rapat',
            'nama_penandatangan' => 'H. Surya Permana',
            'created_by' => $user->id,
        ]);

        $mail = OutgoingMail::first();

        Storage::disk('public')->assertExists($mail->logo_kiri_path);
        Storage::disk('public')->assertMissing($mail->logo_kanan_path);
        $this->assertNull($mail->tanda_tangan_path);
    }

    public function test_store_persists_drawn_signature_and_stempel(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $this->actingAs($user)->post(route('surat-keluar.store'), [
            ...$this->payload(),
            'tanda_tangan' => UploadedFile::fake()->image('tanda-tangan.png', 400, 150),
            'stempel' => UploadedFile::fake()->image('stempel.png', 300, 300),
        ])->assertRedirect();

        $mail = OutgoingMail::first();

        $this->assertNotNull($mail->tanda_tangan_path);
        $this->assertNotNull($mail->stempel_path);

        Storage::disk('public')->assertExists($mail->tanda_tangan_path);
        Storage::disk('public')->assertExists($mail->stempel_path);

        $this->actingAs($user)
            ->get(route('surat-keluar.show', $mail))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('OutgoingMail/Show')
                ->where('mail.tanda_tangan', '/storage/'.$mail->tanda_tangan_path)
                ->where('mail.stempel', '/storage/'.$mail->stempel_path));
    }

    public function test_store_rejects_invalid_asset(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $this->actingAs($user)->post(route('surat-keluar.store'), [
            ...$this->payload(),
            'stempel' => UploadedFile::fake()->create('virus.pdf', 100, 'application/pdf'),
        ])->assertSessionHasErrors(['stempel']);

        $this->assertDatabaseCount('outgoing_mails', 0);
    }

    public function test_index_filters_by_search(): void
    {
        $user = User::factory()->create();

        OutgoingMail::factory()->create(['perihal' => 'Permohonan IMB']);
        OutgoingMail::factory()->create(['perihal' => 'Undangan Musdes']);

        $this->actingAs($user)
            ->get(route('surat-keluar.index', ['search' => 'Musdes']))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('OutgoingMail/Index')
                ->where('mails.meta.total', 1)
                ->where('mails.data.0.perihal', 'Undangan Musdes'));
    }

    public function test_show_renders_letter_with_creator(): void
    {
        $user = User::factory()->create();
        $mail = OutgoingMail::factory()->create(['created_by' => $user->id]);

        $this->actingAs($user)
            ->get(route('surat-keluar.show', $mail))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('OutgoingMail/Show')
                ->has('mail')
                ->where('mail.created_by', $user->name));
    }

    public function test_destroy_removes_letter_and_assets(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $mail = OutgoingMail::factory()->create([
            'created_by' => $user->id,
            'logo_kiri_path' => 'surat-keluar/logo-kiri.png',
            'stempel_path' => 'surat-keluar/stempel.png',
        ]);

        Storage::disk('public')->put($mail->logo_kiri_path, 'x');
        Storage::disk('public')->put($mail->stempel_path, 'x');

        $this->actingAs($user)
            ->delete(route('surat-keluar.destroy', $mail))
            ->assertRedirect();

        $this->assertDatabaseMissing('outgoing_mails', ['id' => $mail->id]);
        Storage::disk('public')->assertMissing($mail->logo_kiri_path);
        Storage::disk('public')->assertMissing($mail->stempel_path);
    }
}
