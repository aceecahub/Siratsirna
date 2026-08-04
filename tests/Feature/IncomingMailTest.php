<?php

namespace Tests\Feature;

use App\Models\IncomingMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class IncomingMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('surat-masuk.index'));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('IncomingMail/Index')
                ->has('mails.data')
                ->has('categories'));
    }

    public function test_store_uploads_scan_and_generates_sequential_agenda_number(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $year = now()->year;

        $payload = [
            'file' => UploadedFile::fake()->create('scan.pdf', 512, 'application/pdf'),
            'tanggal_surat' => now()->toDateString(),
            'tanggal_diterima' => now()->toDateString(),
            'pengirim' => 'Kecamatan Test',
            'perihal' => 'Undangan Rapat',
            'kategori' => 'Undangan',
        ];

        $this->actingAs($user)->post(route('surat-masuk.store'), $payload)
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('incoming_mails', [
            'nomor_agenda' => "ARS-MASUK/{$year}/001",
            'pengirim' => 'Kecamatan Test',
            'created_by' => $user->id,
        ]);

        $mail = IncomingMail::first();

        Storage::disk('public')->assertExists($mail->file_path);
        $this->assertSame('pdf', $mail->file_extension);

        $this->actingAs($user)->post(route('surat-masuk.store'), $payload)
            ->assertRedirect();

        $this->assertDatabaseHas('incoming_mails', [
            'nomor_agenda' => "ARS-MASUK/{$year}/002",
        ]);
    }

    public function test_store_validates_upload_requirements(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('surat-masuk.store'), [
                'file' => UploadedFile::fake()->create('scan.txt', 100, 'text/plain'),
                'tanggal_surat' => now()->toDateString(),
                'tanggal_diterima' => now()->toDateString(),
                'pengirim' => 'Kecamatan Test',
                'perihal' => 'Undangan Rapat',
                'kategori' => 'Tidak Ada',
            ])
            ->assertSessionHasErrors(['file', 'kategori']);
    }

    public function test_destroy_removes_record_and_stored_file(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $mail = IncomingMail::factory()->create(['created_by' => $user->id]);

        Storage::disk('public')->put($mail->file_path, 'dummy');

        $this->actingAs($user)
            ->delete(route('surat-masuk.destroy', $mail))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('incoming_mails', ['id' => $mail->id]);
        Storage::disk('public')->assertMissing($mail->file_path);
    }

    public function test_download_streams_original_file(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $mail = IncomingMail::factory()->create(['created_by' => $user->id]);

        Storage::disk('public')->put($mail->file_path, 'dummy-content');

        $this->actingAs($user)
            ->get(route('surat-masuk.download', $mail))
            ->assertOk()
            ->assertDownload($mail->file_name);
    }
}
