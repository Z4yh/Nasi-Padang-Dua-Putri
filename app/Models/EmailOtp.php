<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailOtp extends Model
{
    use HasFactory;

    protected $table = 'tigaputri_md_email_otp';
    protected $primaryKey = 'id_otp';

    protected $fillable = [
        'id_user',
        'kode_otp',
        'data_tertunda',
        'waktu_kadaluarsa',
    ];

    protected $casts = [
        'waktu_kadaluarsa' => 'datetime',
    ];

    /**
     * Relasi ke User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
