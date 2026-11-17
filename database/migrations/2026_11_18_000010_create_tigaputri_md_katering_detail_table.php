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
        Schema::create('tigaputri_md_katering_detail', function (Blueprint $table) {
            $table->id('id_katering_detail');
            $table->unsignedBigInteger('id_katering');
            $table->unsignedBigInteger('id_menu');
            $table->integer('jumlah_boks');
            $table->decimal('harga_satuan', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();

            $table->foreign('id_katering')
                  ->references('id_katering')
                  ->on('tigaputri_md_katering_order')
                  ->onDelete('cascade');

            $table->foreign('id_menu')
                  ->references('id_menu')
                  ->on('tigaputri_md_menu')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tigaputri_md_katering_detail');
    }
};
