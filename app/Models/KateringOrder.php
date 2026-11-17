<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KateringOrder extends Model
{
    use HasFactory;

    protected $table = 'tigaputri_md_katering_order';
    protected $primaryKey = 'id_katering';

    protected $fillable = [
        'nomor_pesanan',
        'id_user',
        'nama_pemesan',
        'no_whatsapp',
        'tanggal_kirim',
        'jam_kirim',
        'total_boks_pesan',
        'total_boks_bonus',
        'total_tagihan',
        'nominal_dp',
        'sisa_tagihan',
        'bukti_bayar',
        'status_pesanan',
    ];

    protected $casts = [
        'tanggal_kirim' => 'date',
        'total_boks_pesan' => 'integer',
        'total_boks_bonus' => 'integer',
        'total_tagihan' => 'decimal:2',
        'nominal_dp' => 'decimal:2',
        'sisa_tagihan' => 'decimal:2',
    ];

    /**
     * Relasi ke User / Pelanggan (nullable).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Relasi ke Rincian Detail Katering.
     */
    public function details(): HasMany
    {
        return $this->hasMany(KateringDetail::class, 'id_katering', 'id_katering');
    }
}
