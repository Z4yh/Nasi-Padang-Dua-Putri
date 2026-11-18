<?php

namespace Database\Factories;

use App\Models\KateringOrder;
use App\Models\SettingKatering;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KateringOrder>
 */
class KateringOrderFactory extends Factory
{
    protected $model = KateringOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $pelanggan = User::where('role', 'pelanggan')->first();
        $setting = SettingKatering::first();
        $minOrderBonus = $setting?->min_order_bonus ?? 20;
        $bonusBox = $setting?->jumlah_bonus_box ?? 1;

        $totalBoks = fake()->randomElement([15, 20, 25, 30, 40, 50]);
        $totalBonus = ($totalBoks >= $minOrderBonus) ? $bonusBox : 0;
        $hargaPerBoks = 25000;
        $totalTagihan = $totalBoks * $hargaPerBoks;

        $status = fake()->randomElement(['menunggu_dp', 'diproses', 'siap_kirim', 'selesai', 'dibatalkan']);
        $dp = 0;
        $sisa = $totalTagihan;

        if (in_array($status, ['menunggu_dp', 'diproses', 'siap_kirim'])) {
            // 1. Status aktif: WAJIB di masa depan (minimal 1 hari ke depan)
            $tanggalKirim = now()->addDays(fake()->numberBetween(1, 7))->toDateString();
            $createdAt = now()->subDays(fake()->numberBetween(0, 2));

            if (in_array($status, ['diproses', 'siap_kirim'])) {
                $dp = $totalTagihan * 0.5;
                $sisa = $totalTagihan - $dp;
            }
        } elseif ($status === 'selesai') {
            // 2. Status selesai: di masa lalu dengan DP lunas dan sisa tagihan 0
            $hariLalu = fake()->numberBetween(1, 30);
            $tanggalKirim = now()->subDays($hariLalu)->toDateString();
            $createdAt = now()->subDays($hariLalu + fake()->numberBetween(1, 3));
            $dp = $totalTagihan;
            $sisa = 0;
        } else {
            // 3. Status dibatalkan: di masa depan atau hari ini saja (0 s/d 5 hari ke depan)
            $tanggalKirim = now()->addDays(fake()->numberBetween(0, 5))->toDateString();
            $createdAt = now()->subDays(fake()->numberBetween(0, 2));
            $dp = 0;
            $sisa = $totalTagihan;
        }

        return [
            'nomor_pesanan' => 'INV/TP-KTR/' . $createdAt->format('Y/m') . '/' . fake()->unique()->numerify('###'),
            'id_user' => fake()->boolean(70) ? ($pelanggan?->id_user ?? 3) : null,
            'nama_pemesan' => fake()->name(),
            'no_whatsapp' => '08' . fake()->numerify('##########'),
            'tanggal_kirim' => $tanggalKirim,
            'jam_kirim' => fake()->randomElement(['10:00:00', '11:30:00', '12:00:00', '16:00:00']),
            'total_boks_pesan' => $totalBoks,
            'total_boks_bonus' => $totalBonus,
            'total_tagihan' => $totalTagihan,
            'nominal_dp' => $dp,
            'sisa_tagihan' => $sisa,
            'bukti_bayar' => in_array($status, ['diproses', 'siap_kirim', 'selesai']) ? 'bukti_bayar/sample.jpg' : null,
            'status_pesanan' => $status,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];
    }
}
