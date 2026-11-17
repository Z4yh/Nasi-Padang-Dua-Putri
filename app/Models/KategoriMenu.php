<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriMenu extends Model
{
    use HasFactory;

    protected $table = 'tp_md_kategori_menu';
    protected $primaryKey = 'id_kategori_menu';

    protected $fillable = [
        'nama_kategori',
        'slug_kategori',
        'urutan',
    ];

    protected $casts = [
        'urutan' => 'integer',
    ];

    /**
     * Relasi ke data menu (hasMany).
     */
    public function menu(): HasMany
    {
        return $this->hasMany(Menu::class, 'id_kategori_menu', 'id_kategori_menu');
    }

    /**
     * Alias plural relasi ke data menu.
     */
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class, 'id_kategori_menu', 'id_kategori_menu');
    }
}
