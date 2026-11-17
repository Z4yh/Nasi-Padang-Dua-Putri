<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsMenuFavorit extends Model
{
    use HasFactory;

    protected $table = 'tigaputri_md_cms_menu_favorit';
    protected $primaryKey = 'id_menu_favorit';

    protected $fillable = [
        'id_menu',
        'badge_khusus',
        'urutan_tampil',
    ];

    protected $casts = [
        'urutan_tampil' => 'integer',
    ];

    /**
     * Relasi ke Menu Masakan.
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'id_menu', 'id_menu');
    }
}
