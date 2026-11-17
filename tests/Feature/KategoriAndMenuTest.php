<?php

namespace Tests\Feature;

use App\Models\KategoriMenu;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KategoriAndMenuTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_authenticated_user_can_view_kategori_and_menu_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('kategori-menu.index'));
        $response->assertStatus(200);

        $responseMenu = $this->actingAs($this->user)->get(route('menu.index'));
        $responseMenu->assertStatus(200);
    }

    public function test_can_create_kategori_menu_with_auto_generated_slug(): void
    {
        $response = $this->actingAs($this->user)->post(route('kategori-menu.store'), [
            'nama_kategori' => 'Lauk Pauk Utama',
            'urutan'        => 1,
        ]);

        $response->assertRedirect(route('kategori-menu.index'));
        $this->assertDatabaseHas('tp_md_kategori_menu', [
            'nama_kategori' => 'Lauk Pauk Utama',
            'slug_kategori' => 'lauk-pauk-utama',
            'urutan'        => 1,
        ]);
    }

    public function test_kategori_validation_fails_on_duplicate_name(): void
    {
        KategoriMenu::create([
            'nama_kategori' => 'Minuman',
            'slug_kategori' => 'minuman',
            'urutan'        => 1,
        ]);

        $response = $this->actingAs($this->user)->post(route('kategori-menu.store'), [
            'nama_kategori' => 'Minuman',
            'urutan'        => 2,
        ]);

        $response->assertSessionHasErrors(['nama_kategori']);
    }

    public function test_kategori_cannot_be_deleted_if_it_has_related_menus(): void
    {
        $kategori = KategoriMenu::create([
            'nama_kategori' => 'Lauk',
            'slug_kategori' => 'lauk',
            'urutan'        => 1,
        ]);

        Menu::create([
            'id_kategori_menu' => $kategori->id_kategori_menu,
            'nama_menu'        => 'Rendang Daging',
            'slug_menu'        => 'rendang-daging',
            'harga'            => 25000,
            'status_stok'      => 'tersedia',
        ]);

        $response = $this->actingAs($this->user)->delete(route('kategori-menu.destroy', $kategori));
        $response->assertRedirect(route('kategori-menu.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('tp_md_kategori_menu', [
            'id_kategori_menu' => $kategori->id_kategori_menu,
        ]);
    }

    public function test_can_create_menu_with_photo_upload(): void
    {
        Storage::fake('public');

        $kategori = KategoriMenu::create([
            'nama_kategori' => 'Gulai',
            'slug_kategori' => 'gulai',
            'urutan'        => 1,
        ]);

        $file = UploadedFile::fake()->image('gulai-tunjang.png', 400, 400)->size(500);

        $response = $this->actingAs($this->user)->post(route('menu.store'), [
            'id_kategori_menu' => $kategori->id_kategori_menu,
            'nama_menu'        => 'Gulai Tunjang Padang',
            'harga'            => 30000,
            'deskripsi'        => 'Kikil tunjang sapi lembut kuah gulai rempah.',
            'status_stok'      => 'tersedia',
            'foto_menu'        => $file,
        ]);

        $response->assertRedirect(route('menu.index'));

        $menu = Menu::where('nama_menu', 'Gulai Tunjang Padang')->first();
        $this->assertNotNull($menu);
        $this->assertEquals('gulai-tunjang-padang', $menu->slug_menu);
        $this->assertNotNull($menu->foto_menu);

        Storage::disk('public')->assertExists($menu->foto_menu);
    }

    public function test_menu_photo_validation_rejects_invalid_file_and_size(): void
    {
        Storage::fake('public');

        $kategori = KategoriMenu::create([
            'nama_kategori' => 'Sayuran',
            'slug_kategori' => 'sayuran',
            'urutan'        => 1,
        ]);

        // File too large (> 2048 KB)
        $largeFile = UploadedFile::fake()->image('besar.jpg')->size(3000);

        $response = $this->actingAs($this->user)->post(route('menu.store'), [
            'id_kategori_menu' => $kategori->id_kategori_menu,
            'nama_menu'        => 'Daun Singkong Rebus',
            'harga'            => 5000,
            'status_stok'      => 'tersedia',
            'foto_menu'        => $largeFile,
        ]);

        $response->assertSessionHasErrors(['foto_menu']);

        // Invalid extension (e.g. PDF)
        $pdfFile = UploadedFile::fake()->create('menu.pdf', 100, 'application/pdf');

        $responsePdf = $this->actingAs($this->user)->post(route('menu.store'), [
            'id_kategori_menu' => $kategori->id_kategori_menu,
            'nama_menu'        => 'Sayur Kapau',
            'harga'            => 8000,
            'status_stok'      => 'tersedia',
            'foto_menu'        => $pdfFile,
        ]);

        $responsePdf->assertSessionHasErrors(['foto_menu']);
    }

    public function test_updating_menu_photo_replaces_and_deletes_old_photo(): void
    {
        Storage::fake('public');

        $kategori = KategoriMenu::create([
            'nama_kategori' => 'Ayam',
            'slug_kategori' => 'ayam',
            'urutan'        => 1,
        ]);

        $oldFile = UploadedFile::fake()->image('ayam-lama.jpg');
        $oldPath = $oldFile->store('menu-images', 'public');

        $menu = Menu::create([
            'id_kategori_menu' => $kategori->id_kategori_menu,
            'nama_menu'        => 'Ayam Pop',
            'slug_menu'        => 'ayam-pop',
            'harga'            => 20000,
            'status_stok'      => 'tersedia',
            'foto_menu'        => $oldPath,
        ]);

        Storage::disk('public')->assertExists($oldPath);

        $newFile = UploadedFile::fake()->image('ayam-baru.webp');

        $response = $this->actingAs($this->user)->put(route('menu.update', $menu), [
            'id_kategori_menu' => $kategori->id_kategori_menu,
            'nama_menu'        => 'Ayam Pop Spesial',
            'harga'            => 22000,
            'status_stok'      => 'tersedia',
            'foto_menu'        => $newFile,
        ]);

        $response->assertRedirect(route('menu.index'));

        $menu->refresh();
        $this->assertEquals('Ayam Pop Spesial', $menu->nama_menu);
        $this->assertNotEquals($oldPath, $menu->foto_menu);

        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($menu->foto_menu);
    }

    public function test_toggle_status_flips_between_tersedia_and_habis(): void
    {
        $kategori = KategoriMenu::create([
            'nama_kategori' => 'Ikan',
            'slug_kategori' => 'ikan',
            'urutan'        => 1,
        ]);

        $menu = Menu::create([
            'id_kategori_menu' => $kategori->id_kategori_menu,
            'nama_menu'        => 'Ikan Bakar Padang',
            'slug_menu'        => 'ikan-bakar-padang',
            'harga'            => 24000,
            'status_stok'      => 'tersedia',
        ]);

        $response = $this->actingAs($this->user)->patch(route('menu.toggle-status', $menu));
        $response->assertRedirect();

        $menu->refresh();
        $this->assertEquals('habis', $menu->status_stok);

        $this->actingAs($this->user)->patch(route('menu.toggle-status', $menu));
        $menu->refresh();
        $this->assertEquals('tersedia', $menu->status_stok);
    }
}
