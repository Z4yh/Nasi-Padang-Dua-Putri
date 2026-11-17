<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'tigaputri_md_users';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'name',
        'nama_lengkap',
        'username',
        'email',
        'password',
        'role',
        'status_aktif',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'status_aktif' => 'integer',
        ];
    }

    /**
     * Boot hook untuk memastikan username, role, dan status_aktif terisi secara default.
     */
    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->username)) {
                $base = Str::slug(explode('@', $user->email ?? 'user')[0], '_');
                $username = $base ?: 'user_' . Str::random(5);
                $count = 1;
                while (static::where('username', $username)->exists()) {
                    $username = $base . '_' . $count;
                    $count++;
                }
                $user->username = $username;
            }

            if (empty($user->role)) {
                $user->role = 'pelanggan';
            }

            if (!isset($user->status_aktif)) {
                $user->status_aktif = 1;
            }
        });
    }

    /**
     * Alias accessor & mutator agar kompatibel dengan kode / view Breeze default ('name' & 'id').
     */
    public function getIdAttribute(): mixed
    {
        return $this->attributes['id_user'] ?? null;
    }

    public function getNameAttribute(): ?string
    {
        return $this->nama_lengkap;
    }

    public function setNameAttribute(?string $value): void
    {
        $this->attributes['nama_lengkap'] = $value;
    }

    /**
     * Virtual mapping email_verified_at ke status_aktif.
     */
    public function getEmailVerifiedAtAttribute(): ?Carbon
    {
        return ($this->status_aktif ?? 1) ? ($this->created_at ?? now()) : null;
    }

    public function setEmailVerifiedAtAttribute($value): void
    {
        $this->attributes['status_aktif'] = $value ? 1 : 0;
    }

    public function hasVerifiedEmail(): bool
    {
        return ($this->status_aktif ?? 1) == 1;
    }

    public function markEmailAsVerified(): bool
    {
        return $this->forceFill(['status_aktif' => 1])->save();
    }

    /**
     * Relasi ke Email OTP.
     */
    public function emailOtps(): HasMany
    {
        return $this->hasMany(EmailOtp::class, 'id_user', 'id_user');
    }

    /**
     * Relasi ke Transaksi Kasir.
     */
    public function transaksiKasir(): HasMany
    {
        return $this->hasMany(TransaksiKasir::class, 'id_user', 'id_user');
    }

    /**
     * Relasi ke Tutup Kasir.
     */
    public function tutupKasir(): HasMany
    {
        return $this->hasMany(TutupKasir::class, 'id_user', 'id_user');
    }

    /**
     * Relasi ke Pesanan Katering.
     */
    public function kateringOrders(): HasMany
    {
        return $this->hasMany(KateringOrder::class, 'id_user', 'id_user');
    }

    /**
     * Relasi ke Pengeluaran Operasional.
     */
    public function pengeluaran(): HasMany
    {
        return $this->hasMany(Pengeluaran::class, 'id_user', 'id_user');
    }
}
