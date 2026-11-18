<?php

namespace Database\Factories;

use App\Models\KategoriPengeluaran;
use App\Models\Pengeluaran;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pengeluaran>
 */
class PengeluaranFactory extends Factory
{
    protected $model = Pengeluaran::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::whereIn('role', ['super_admin', 'penjual'])->first();
        $kategori = KategoriPengeluaran::first();

        return [
            'id_user' => $user?->id_user ?? 1,
            'id_kategori_pengeluaran' => $kategori?->id_kategori_pengeluaran ?? 1,
            'tanggal_pengeluaran' => now()->subDays(fake()->numberBetween(0, 30))->toDateString(),
            'nominal' => fake()->randomElement([50000, 75000, 100000, 150000, 250000, 500000]),
            'sumber_dana' => fake()->randomElement(['kas_laci', 'rekening_bank', 'pribadi']),
            'keterangan' => fake()->sentence(4),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
