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
     * Label teks berbahasa Indonesia untuk setiap nilai enum status pesanan.
     */
    public static function daftarLabelStatus(): array
    {
        return [
            'menunggu_dp' => 'Belum DP',
            'diproses'    => 'Dimasak',
            'siap_kirim'  => 'Siap Kirim',
            'selesai'     => 'Selesai',
            'dibatalkan'  => 'Dibatalkan',
        ];
    }

    /**
     * Aturan urutan alur status pesanan katering:
     * - menunggu_dp -> diproses atau dibatalkan
     * - diproses    -> siap_kirim atau dibatalkan
     * - siap_kirim  -> selesai atau dibatalkan
     * - selesai & dibatalkan terkunci (tidak ada pilihan berikutnya)
     *
     * @return array<string>
     */
    public function statusBerikutnya(): array
    {
        return match ($this->status_pesanan) {
            'menunggu_dp' => ['diproses', 'dibatalkan'],
            'diproses'    => ['siap_kirim', 'dibatalkan'],
            'siap_kirim'  => ['selesai', 'dibatalkan'],
            default       => [],
        };
    }

    /**
     * Cek apakah pesanan diizinkan berpindah ke status target.
     */
    public function bisaUbahKeStatus(string $statusBaru): bool
    {
        return in_array($statusBaru, $this->statusBerikutnya(), true);
    }

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
