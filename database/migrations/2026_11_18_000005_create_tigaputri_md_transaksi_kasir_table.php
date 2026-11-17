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
        Schema::create('tigaputri_md_transaksi_kasir', function (Blueprint $table) {
            $table->id('id_transaksi');
            $table->string('nomor_transaksi', 50)->unique();
            $table->unsignedBigInteger('id_user');
            $table->enum('metode_pembayaran', ['tunai', 'qris', 'transfer'])->default('tunai');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total_bayar', 12, 2);
            $table->decimal('kembalian', 12, 2)->default(0);
            $table->timestamps();

            $table->foreign('id_user')
                  ->references('id_user')
                  ->on('tigaputri_md_users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tigaputri_md_transaksi_kasir');
    }
};
