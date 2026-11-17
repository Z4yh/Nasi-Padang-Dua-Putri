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
        Schema::create('tigaputri_md_tutup_kasir', function (Blueprint $table) {
            $table->id('id_tutup_kasir');
            $table->unsignedBigInteger('id_user');
            $table->date('tanggal_tutup');
            $table->decimal('total_kas_fisik', 12, 2);
            $table->decimal('total_mutasi_qris', 12, 2);
            $table->decimal('selisih', 12, 2)->default(0);
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
        Schema::dropIfExists('tigaputri_md_tutup_kasir');
    }
};
