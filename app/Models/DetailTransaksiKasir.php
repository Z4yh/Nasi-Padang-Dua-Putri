<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailTransaksiKasir extends Model
{
    use HasFactory;

    protected $table = 'tigaputri_md_detail_transaksi_kasir';
    protected $primaryKey = 'id_detail_transaksi';

    protected $fillable = [
        'id_transaksi',
        'id_menu',
        'jumlah',
        'harga_satuan',
        'subtotal_item',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'harga_satuan' => 'decimal:2',
        'subtotal_item' => 'decimal:2',
    ];

    /**
     * Relasi ke Transaksi Kasir.
     */
    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(TransaksiKasir::class, 'id_transaksi', 'id_transaksi');
    }

    /**
     * Relasi ke Menu Masakan.
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'id_menu', 'id_menu');
    }
}
