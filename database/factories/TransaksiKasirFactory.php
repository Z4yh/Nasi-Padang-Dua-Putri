<?php

namespace Database\Factories;

use App\Models\TransaksiKasir;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransaksiKasir>
 */
class TransaksiKasirFactory extends Factory
{
    protected $model = TransaksiKasir::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $penjual = User::where('role', 'penjual')->first();
        $subtotal = fake()->randomElement([20000, 25000, 35000, 50000, 75000]);
        $metode = fake()->randomElement(['tunai', 'qris', 'transfer']);
        $totalBayar = $subtotal;
        $kembalian = 0;

        if ($metode === 'tunai') {
            $totalBayar = (int) ceil($subtotal / 10000) * 10000;
            if ($totalBayar === $subtotal) {
                $totalBayar += 10000;
            }
            $kembalian = $totalBayar - $subtotal;
        }

        return [
            'nomor_transaksi' => 'TP-' . now()->format('Ymd') . '-' . fake()->unique()->numerify('####'),
            'id_user' => $penjual?->id_user ?? 2,
            'metode_pembayaran' => $metode,
            'subtotal' => $subtotal,
            'total_bayar' => $totalBayar,
            'kembalian' => $kembalian,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
