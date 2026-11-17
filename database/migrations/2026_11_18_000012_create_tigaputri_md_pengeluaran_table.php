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
        Schema::create('tigaputri_md_pengeluaran', function (Blueprint $table) {
            $table->id('id_pengeluaran');
            $table->unsignedBigInteger('id_user');
            $table->unsignedInteger('id_kategori_pengeluaran');
            $table->date('tanggal_pengeluaran');
            $table->decimal('nominal', 12, 2);
            $table->enum('sumber_dana', ['kas_laci', 'rekening_bank', 'pribadi'])->default('kas_laci');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('id_user')
                  ->references('id_user')
                  ->on('tigaputri_md_users');

            $table->foreign('id_kategori_pengeluaran')
                  ->references('id_kategori_pengeluaran')
                  ->on('tigaputri_md_kategori_pengeluaran')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tigaputri_md_pengeluaran');
    }
};
