<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKategoriMenuRequest;
use App\Http\Requests\UpdateKategoriMenuRequest;
use App\Models\KategoriMenu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class KategoriMenuController extends Controller
{
    /**
     * Tampilkan daftar kategori menu.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $kategoriList = KategoriMenu::withCount('menu')
            ->when($search, function ($query, $search) {
                $query->where('nama_kategori', 'like', "%{$search}%");
            })
            ->orderBy('urutan', 'asc')
            ->orderBy('nama_kategori', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('kategori-menu.index', compact('kategoriList', 'search'));
    }

    /**
     * Tampilkan form tambah kategori menu.
     */
    public function create(): View
    {
        $nextUrutan = (KategoriMenu::max('urutan') ?? 0) + 1;

        return view('kategori-menu.create', compact('nextUrutan'));
    }

    /**
     * Simpan data kategori menu baru.
     */
    public function store(StoreKategoriMenuRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug_kategori'] = $this->generateUniqueSlug($data['nama_kategori']);
        $data['urutan'] = $data['urutan'] ?? ((KategoriMenu::max('urutan') ?? 0) + 1);

        KategoriMenu::create($data);

        return redirect()
            ->route('kategori-menu.index')
            ->with('success', 'Kategori menu "' . $data['nama_kategori'] . '" berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit kategori menu.
     */
    public function edit(KategoriMenu $kategoriMenu): View
    {
        return view('kategori-menu.edit', compact('kategoriMenu'));
    }

    /**
     * Perbarui data kategori menu.
     */
    public function update(UpdateKategoriMenuRequest $request, KategoriMenu $kategoriMenu): RedirectResponse
    {
        $data = $request->validated();
        $data['slug_kategori'] = $this->generateUniqueSlug($data['nama_kategori'], $kategoriMenu->id_kategori_menu);
        $data['urutan'] = $data['urutan'] ?? $kategoriMenu->urutan;

        $kategoriMenu->update($data);

        return redirect()
            ->route('kategori-menu.index')
            ->with('success', 'Kategori menu "' . $kategoriMenu->nama_kategori . '" berhasil diperbarui.');
    }

    /**
     * Hapus kategori menu jika tidak sedang digunakan oleh data menu.
     */
    public function destroy(KategoriMenu $kategoriMenu): RedirectResponse
    {
        $menuCount = $kategoriMenu->menu()->count();

        if ($menuCount > 0) {
            return redirect()
                ->route('kategori-menu.index')
                ->with('error', 'Kategori menu "' . $kategoriMenu->nama_kategori . '" tidak dapat dihapus karena masih digunakan oleh ' . $menuCount . ' item menu.');
        }

        $nama = $kategoriMenu->nama_kategori;
        $kategoriMenu->delete();

        return redirect()
            ->route('kategori-menu.index')
            ->with('success', 'Kategori menu "' . $nama . '" berhasil dihapus.');
    }

    /**
     * Generate slug yang unik untuk kategori menu.
     */
    protected function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug ?: 'kategori';
        $count = 1;

        while (
            KategoriMenu::where('slug_kategori', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id_kategori_menu', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$count}";
            $count++;
        }

        return $slug;
    }
}
