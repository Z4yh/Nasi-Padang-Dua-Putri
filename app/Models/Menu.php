<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'tigaputri_md_menu';
    protected $primaryKey = 'id_menu';

    protected $fillable = [
        'id_kategori_menu',
        'nama_menu',
        'slug_menu',
        'harga',
        'deskripsi',
        'foto_menu',
        'status_stok',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
    ];

    /**
     * Relasi ke kategori menu (belongsTo).
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriMenu::class, 'id_kategori_menu', 'id_kategori_menu');
    }

    /**
     * Relasi ke Detail Transaksi Kasir.
     */
    public function detailTransaksiKasir(): HasMany
    {
        return $this->hasMany(DetailTransaksiKasir::class, 'id_menu', 'id_menu');
    }

    /**
     * Relasi ke Detail Katering.
     */
    public function kateringDetails(): HasMany
    {
        return $this->hasMany(KateringDetail::class, 'id_menu', 'id_menu');
    }

    /**
     * Relasi ke CMS Menu Favorit.
     */
    public function cmsMenuFavorits(): HasMany
    {
        return $this->hasMany(CmsMenuFavorit::class, 'id_menu', 'id_menu');
    }

    /**
     * URL Foto Menu jika tersedia di storage publik.
     */
    public function getFotoUrlAttribute(): ?string
    {
        if ($this->foto_menu && Storage::disk('public')->exists($this->foto_menu)) {
            return Storage::disk('public')->url($this->foto_menu);
        }

        return null;
    }
}
