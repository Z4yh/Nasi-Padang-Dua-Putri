<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Models\KategoriMenu;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MenuController extends Controller
{
    /**
     * Tampilkan katalog menu.
     * Jika diakses oleh staff (penjual / super_admin) dan tidak dalam mode preview publik, tampilkan tabel manajemen menu.
     * Jika diakses oleh pengunjung/guest (atau staff dengan query ?view=public), tampilkan halaman katalog publik.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $kategoriId = $request->query('id_kategori_menu');
        $statusStok = $request->query('status_stok');

        $isStaff = Auth::check() && in_array(Auth::user()->role, ['penjual', 'super_admin']);
        $wantsPublic = $request->query('view') === 'public';

        // Tampilan Admin / Penjual (Tabel Manajemen Menu)
        if ($isStaff && ! $wantsPublic) {
            $menuList = Menu::with('kategori')
                ->when($search, function ($query, $search) {
                    $query->where('nama_menu', 'like', "%{$search}%");
                })
                ->when($kategoriId, function ($query, $kategoriId) {
                    $query->where('id_kategori_menu', $kategoriId);
                })
                ->when($statusStok, function ($query, $statusStok) {
                    $query->where('status_stok', $statusStok);
                })
                ->latest('id_menu')
                ->paginate(10)
                ->withQueryString();

            $kategoriList = KategoriMenu::orderBy('urutan')->get();

            return view('menu.index', compact('menuList', 'kategoriList', 'search', 'kategoriId', 'statusStok'));
        }

        // Tampilan Publik / Pengunjung (Katalog Card Grid Tanpa Login)
        $menuList = Menu::with('kategori')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_menu', 'like', "%{$search}%")
                      ->orWhere('deskripsi', 'like', "%{$search}%");
                });
            })
            ->when($kategoriId, function ($query, $kategoriId) {
                $query->where('id_kategori_menu', $kategoriId);
            })
            ->when($statusStok, function ($query, $statusStok) {
                $query->where('status_stok', $statusStok);
            })
            ->latest('id_menu')
            ->paginate(12)
            ->withQueryString();

        $kategoriList = KategoriMenu::orderBy('urutan')->get();

        return view('menu.public', compact('menuList', 'kategoriList', 'search', 'kategoriId', 'statusStok'));
    }

    /**
     * Tampilkan formulir tambah menu baru.
     */
    public function create(): View
    {
        $kategoriList = KategoriMenu::orderBy('urutan')->get();

        return view('menu.create', compact('kategoriList'));
    }

    /**
     * Simpan menu baru beserta berkas foto menu ke storage.
     */
    public function store(StoreMenuRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug_menu'] = $this->generateUniqueSlug($data['nama_menu']);

        // Upload berkas foto menu ke storage/app/public/menu-images
        if ($request->hasFile('foto_menu')) {
            $data['foto_menu'] = $request->file('foto_menu')->store('menu-images', 'public');
        }

        Menu::create($data);

        return redirect()
            ->route('menu.index')
            ->with('success', 'Menu masakan "' . $data['nama_menu'] . '" berhasil ditambahkan.');
    }

    /**
     * Tampilkan formulir edit menu.
     */
    public function edit(Menu $menu): View
    {
        $kategoriList = KategoriMenu::orderBy('urutan')->get();

        return view('menu.edit', compact('menu', 'kategoriList'));
    }

    /**
     * Perbarui data menu dan ganti foto lama jika ada berkas foto baru yang diunggah.
     */
    public function update(UpdateMenuRequest $request, Menu $menu): RedirectResponse
    {
        $data = $request->validated();
        $data['slug_menu'] = $this->generateUniqueSlug($data['nama_menu'], $menu->id_menu);

        if ($request->hasFile('foto_menu')) {
            // Hapus foto lama jika ada
            if ($menu->foto_menu && Storage::disk('public')->exists($menu->foto_menu)) {
                Storage::disk('public')->delete($menu->foto_menu);
            }

            // Simpan foto baru
            $data['foto_menu'] = $request->file('foto_menu')->store('menu-images', 'public');
        }

        $menu->update($data);

        return redirect()
            ->route('menu.index')
            ->with('success', 'Menu masakan "' . $menu->nama_menu . '" berhasil diperbarui.');
    }

    /**
     * Hapus menu dan berkas fotonya dari storage publik.
     */
    public function destroy(Menu $menu): RedirectResponse
    {
        // Hapus foto dari storage publik jika ada
        if ($menu->foto_menu && Storage::disk('public')->exists($menu->foto_menu)) {
            Storage::disk('public')->delete($menu->foto_menu);
        }

        $namaMenu = $menu->nama_menu;
        $menu->delete();

        return redirect()
            ->route('menu.index')
            ->with('success', 'Menu masakan "' . $namaMenu . '" berhasil dihapus.');
    }

    /**
     * Endpoint cepat untuk mengubah status stok menu (tersedia <-> habis).
     */
    public function toggleStatus(Menu $menu): RedirectResponse
    {
        $newStatus = $menu->status_stok === 'tersedia' ? 'habis' : 'tersedia';
        $menu->update(['status_stok' => $newStatus]);

        $statusText = $newStatus === 'tersedia' ? 'Tersedia' : 'Habis';

        return back()->with('success', 'Status stok menu "' . $menu->nama_menu . '" berhasil diubah menjadi: ' . $statusText . '.');
    }

    /**
     * Generate slug yang unik untuk katalog menu.
     */
    protected function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug ?: 'menu';
        $count = 1;

        while (
            Menu::where('slug_menu', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id_menu', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$count}";
            $count++;
        }

        return $slug;
    }
}
