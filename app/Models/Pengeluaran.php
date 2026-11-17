<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengeluaran extends Model
{
    use HasFactory;

    protected $table = 'tigaputri_md_pengeluaran';
    protected $primaryKey = 'id_pengeluaran';

    protected $fillable = [
        'id_user',
        'id_kategori_pengeluaran',
        'tanggal_pengeluaran',
        'nominal',
        'sumber_dana',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_pengeluaran' => 'date',
        'nominal' => 'decimal:2',
    ];

    /**
     * Relasi ke User / Penanggung Jawab.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Relasi ke Kategori Pengeluaran.
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriPengeluaran::class, 'id_kategori_pengeluaran', 'id_kategori_pengeluaran');
    }
}
