<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KateringDetail extends Model
{
    use HasFactory;

    protected $table = 'tigaputri_md_katering_detail';
    protected $primaryKey = 'id_katering_detail';

    protected $fillable = [
        'id_katering',
        'id_menu',
        'jumlah_boks',
        'harga_satuan',
        'subtotal',
    ];

    protected $casts = [
        'jumlah_boks' => 'integer',
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Relasi ke Pesanan Katering.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(KateringOrder::class, 'id_katering', 'id_katering');
    }

    /**
     * Relasi ke Menu Masakan.
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'id_menu', 'id_menu');
    }
}
