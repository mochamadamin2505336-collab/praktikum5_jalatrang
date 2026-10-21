<?php

namespace Tests\Feature;

use App\Models\Berita;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BeritaImageUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_berita_image_can_be_uploaded_when_creating_a_berita(): void
    {
        Storage::fake('public');

        $response = $this->post(route('berita.store'), [
            'judul' => 'Berita dengan gambar',
            'kategori' => 'Pemuda',
            'isi' => 'Isi berita yang cukup panjang untuk validasi.',
            'gambar' => UploadedFile::fake()->image('thumbnail.jpg'),
        ]);

        $response->assertRedirect(route('berita.index'));

        $berita = Berita::firstOrFail();

        $this->assertDatabaseHas('beritas', [
            'id' => $berita->id,
            'gambar' => $berita->gambar,
        ]);
        Storage::disk('public')->assertExists($berita->gambar);

        $this->get(route('berita.index'))
            ->assertSee(asset('storage/'.$berita->gambar));
        $this->get(route('berita.show', $berita))
            ->assertSee(asset('storage/'.$berita->gambar));
    }

    public function test_invalid_file_is_rejected_as_a_berita_image(): void
    {
        Storage::fake('public');

        $response = $this->post(route('berita.store'), [
            'judul' => 'Berita dengan gambar salah',
            'kategori' => 'Pemuda',
            'isi' => 'Isi berita yang cukup panjang untuk validasi.',
            'gambar' => UploadedFile::fake()->create('dokumen.txt', 10, 'text/plain'),
        ]);

        $response->assertSessionHasErrors('gambar');
        $this->assertDatabaseCount('beritas', 0);
    }

    public function test_new_image_replaces_the_existing_berita_image(): void
    {
        Storage::fake('public');

        $gambarLama = 'berita/gambar-lama.jpg';
        Storage::disk('public')->put($gambarLama, 'gambar lama');

        $berita = Berita::create([
            'judul' => 'Judul lama',
            'kategori' => 'Pemuda',
            'gambar' => $gambarLama,
            'isi' => 'Isi berita yang cukup panjang untuk validasi.',
        ]);

        $response = $this->put(route('berita.update', $berita), [
            'judul' => 'Judul baru',
            'kategori' => 'Pemuda',
            'isi' => 'Isi berita yang cukup panjang untuk validasi.',
            'gambar' => UploadedFile::fake()->image('thumbnail-baru.jpg'),
        ]);

        $response->assertRedirect(route('berita.index'));

        $berita->refresh();

        $this->assertNotSame($gambarLama, $berita->gambar);
        $this->assertDatabaseHas('beritas', [
            'id' => $berita->id,
            'gambar' => $berita->gambar,
        ]);
        Storage::disk('public')->assertExists($berita->gambar);
        Storage::disk('public')->assertMissing($gambarLama);
    }
}
