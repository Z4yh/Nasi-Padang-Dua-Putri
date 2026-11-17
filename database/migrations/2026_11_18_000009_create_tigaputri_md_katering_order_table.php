<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tigaputri_md_katering_order', function (Blueprint $table) {
            $table->id('id_katering');
            $table->string('nomor_pesanan', 50)->unique();
            $table->unsignedBigInteger('id_user')->nullable();
            $table->string('nama_pemesan', 100);
            $table->string('no_whatsapp', 20);
            $table->date('tanggal_kirim');
            $table->time('jam_kirim');
            $table->integer('total_boks_pesan');
            $table->integer('total_boks_bonus')->default(0);
            $table->decimal('total_tagihan', 12, 2);
            $table->decimal('nominal_dp', 12, 2)->default(0);
            $table->decimal('sisa_tagihan', 12, 2)->default(0);
            $table->string('bukti_bayar', 255)->nullable();
            $table->enum('status_pesanan', ['menunggu_dp', 'diproses', 'siap_kirim', 'selesai', 'dibatalkan'])->default('menunggu_dp');
            $table->timestamps();

            $table->foreign('id_user')
                  ->references('id_user')
                  ->on('tigaputri_md_users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tigaputri_md_katering_order');
    }
};
