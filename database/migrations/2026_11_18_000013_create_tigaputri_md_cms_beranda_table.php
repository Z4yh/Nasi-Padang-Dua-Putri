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
        Schema::create('tigaputri_md_cms_beranda', function (Blueprint $table) {
            $table->increments('id_cms_beranda');
            $table->string('hero_title', 150);
            $table->string('foto_warung', 255)->nullable();
            $table->string('jam_buka', 100);
            $table->string('no_whatsapp_reservasi', 20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tigaputri_md_cms_beranda');
    }
};
