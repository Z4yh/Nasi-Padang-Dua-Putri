<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriPengeluaran extends Model
{
    use HasFactory;

    protected $table = 'tigaputri_md_kategori_pengeluaran';
    protected $primaryKey = 'id_kategori_pengeluaran';

    protected $fillable = [
        'nama_kategori_pengeluaran',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'integer',
    ];

    /**
     * Relasi ke Data Pengeluaran.
     */
    public function pengeluaran(): HasMany
    {
        return $this->hasMany(Pengeluaran::class, 'id_kategori_pengeluaran', 'id_kategori_pengeluaran');
    }
}
