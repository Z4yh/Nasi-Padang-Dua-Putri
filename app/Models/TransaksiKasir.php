<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransaksiKasir extends Model
{
    use HasFactory;

    protected $table = 'tigaputri_md_transaksi_kasir';
    protected $primaryKey = 'id_transaksi';

    protected $fillable = [
        'nomor_transaksi',
        'id_user',
        'metode_pembayaran',
        'subtotal',
        'total_bayar',
        'kembalian',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'total_bayar' => 'decimal:2',
        'kembalian' => 'decimal:2',
    ];

    /**
     * Relasi ke User / Kasir.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Relasi ke Detail Transaksi Kasir.
     */
    public function detailTransaksi(): HasMany
    {
        return $this->hasMany(DetailTransaksiKasir::class, 'id_transaksi', 'id_transaksi');
    }
}
