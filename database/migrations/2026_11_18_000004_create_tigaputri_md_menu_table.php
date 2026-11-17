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
        Schema::create('tigaputri_md_menu', function (Blueprint $table) {
            $table->id('id_menu');
            $table->unsignedInteger('id_kategori_menu');
            $table->string('nama_menu', 100);
            $table->string('slug_menu', 120)->unique();
            $table->decimal('harga', 12, 2);
            $table->text('deskripsi')->nullable();
            $table->string('foto_menu', 255)->nullable();
            $table->enum('status_stok', ['tersedia', 'habis'])->default('tersedia');
            $table->timestamps();

            $table->foreign('id_kategori_menu')
                  ->references('id_kategori_menu')
                  ->on('tigaputri_md_kategori_menu')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tigaputri_md_menu');
    }
};
