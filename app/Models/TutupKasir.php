<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TutupKasir extends Model
{
    use HasFactory;

    protected $table = 'tigaputri_md_tutup_kasir';
    protected $primaryKey = 'id_tutup_kasir';

    protected $fillable = [
        'id_user',
        'tanggal_tutup',
        'total_kas_fisik',
        'total_mutasi_qris',
        'selisih',
    ];

    protected $casts = [
        'tanggal_tutup' => 'date',
        'total_kas_fisik' => 'decimal:2',
        'total_mutasi_qris' => 'decimal:2',
        'selisih' => 'decimal:2',
    ];

    /**
     * Relasi ke User / Kasir.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
