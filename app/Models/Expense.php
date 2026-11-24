<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use InvalidArgumentException;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'expenses';

    protected $fillable = [
        'category',
        'amount',
        'note',
        'fund_source',
        'occurred_at',
        'attachment_url',
        'created_by',
    ];

    protected $casts = [
        'category' => 'string',
        'amount' => 'integer',
        'fund_source' => 'string',
        'occurred_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public const CATEGORIES = [
        'bahan_pasar',
        'gas_galon',
        'upah_makan',
        'listrik_air',
        'lain_lain',
    ];

    public const FUND_SOURCES = [
        'kas_tunai',
        'transfer_bank',
    ];

    protected static function booted(): void
    {
        static::saving(function (Expense $expense) {
            if ($expense->amount <= 0) {
                throw new InvalidArgumentException('Nominal pengeluaran (amount) harus berupa bilangan bulat lebih dari 0.');
            }

            if ($expense->note !== null && mb_strlen($expense->note) > 200) {
                throw new InvalidArgumentException('Catatan pengeluaran (note) maksimal 200 karakter.');
            }

            if (!in_array($expense->category, self::CATEGORIES, true)) {
                throw new InvalidArgumentException('Kategori pengeluaran tidak valid.');
            }

            if (!in_array($expense->fund_source, self::FUND_SOURCES, true)) {
                throw new InvalidArgumentException('Sumber dana pengeluaran tidak valid.');
            }
        });
    }

    /**
     * Relasi ke User pembuat pengeluaran.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }

    /**
     * Alias untuk relasi creator / user.
     */
    public function user(): BelongsTo
    {
        return $this->creator();
    }
}
