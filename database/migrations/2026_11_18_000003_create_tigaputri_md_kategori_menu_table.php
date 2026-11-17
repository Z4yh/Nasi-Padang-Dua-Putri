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
        Schema::create('tigaputri_md_kategori_menu', function (Blueprint $table) {
            $table->increments('id_kategori_menu');
            $table->string('nama_kategori', 50);
            $table->string('slug_kategori', 60)->unique();
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tigaputri_md_kategori_menu');
    }
};
