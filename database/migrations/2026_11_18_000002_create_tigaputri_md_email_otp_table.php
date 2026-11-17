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
        Schema::create('tigaputri_md_email_otp', function (Blueprint $table) {
            $table->id('id_otp');
            $table->unsignedBigInteger('id_user');
            $table->string('kode_otp', 10);
            $table->longText('data_tertunda')->nullable();
            $table->dateTime('waktu_kadaluarsa');
            $table->timestamps();

            $table->foreign('id_user')
                  ->references('id_user')
                  ->on('tigaputri_md_users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tigaputri_md_email_otp');
    }
};
