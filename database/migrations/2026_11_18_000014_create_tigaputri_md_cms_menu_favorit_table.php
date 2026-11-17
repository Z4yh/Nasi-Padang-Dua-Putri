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
        Schema::create('tigaputri_md_cms_menu_favorit', function (Blueprint $table) {
            $table->increments('id_menu_favorit');
            $table->unsignedBigInteger('id_menu');
            $table->string('badge_khusus', 50)->nullable();
            $table->integer('urutan_tampil')->default(0);
            $table->timestamps();

            $table->foreign('id_menu')
                  ->references('id_menu')
                  ->on('tigaputri_md_menu')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tigaputri_md_cms_menu_favorit');
    }
};
