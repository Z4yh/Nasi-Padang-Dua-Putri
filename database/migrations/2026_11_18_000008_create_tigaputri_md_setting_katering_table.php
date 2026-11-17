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
        Schema::create('tigaputri_md_setting_katering', function (Blueprint $table) {
            $table->increments('id_setting_katering');
            $table->integer('min_order_bonus')->default(20);
            $table->integer('jumlah_bonus_box')->default(1);
            $table->integer('min_dp_persen')->default(50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tigaputri_md_setting_katering');
    }
};
