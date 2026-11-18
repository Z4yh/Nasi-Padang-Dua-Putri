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
        $tanggalKirim = now()->addDays(fake()->numberBetween(1, 7))->toDateString();

        if ($status === 'selesai') {
            $tanggalKirim = now()->subDays(fake()->numberBetween(1, 30))->toDateString();
            $dp = $totalTagihan;
            $sisa = 0;
        } elseif (in_array($status, ['diproses', 'siap_kirim'])) {
            $dp = $totalTagihan * 0.5;
            $sisa = $totalTagihan - $dp;
        }

        return [
            'nomor_pesanan' => 'INV/TP-KTR/' . now()->format('Y/m') . '/' . fake()->unique()->numerify('###'),
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
            'created_at' => now()->subDays(1),
            'updated_at' => now(),
        ];
    }
}
