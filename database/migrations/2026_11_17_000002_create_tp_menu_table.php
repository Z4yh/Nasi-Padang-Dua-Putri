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
        Schema::create('tp_menu', function (Blueprint $table) {
            $table->id('id_menu');
            $table->unsignedBigInteger('id_kategori_menu');
            $table->string('nama_menu');
            $table->string('slug_menu')->unique();
            $table->decimal('harga', 12, 2)->default(0);
            $table->text('deskripsi')->nullable();
            $table->string('foto_menu')->nullable();
            $table->enum('status_stok', ['tersedia', 'habis'])->default('tersedia');
            $table->timestamps();

            $table->foreign('id_kategori_menu')
                  ->references('id_kategori_menu')
                  ->on('tp_md_kategori_menu')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tp_menu');
    }
};
