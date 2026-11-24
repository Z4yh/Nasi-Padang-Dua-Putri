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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->enum('category', [
                'bahan_pasar',
                'gas_galon',
                'upah_makan',
                'listrik_air',
                'lain_lain',
            ]);
            $table->unsignedBigInteger('amount');
            $table->string('note', 200)->nullable();
            $table->enum('fund_source', [
                'kas_tunai',
                'transfer_bank',
            ])->default('kas_tunai');
            $table->timestamp('occurred_at')->useCurrent();
            $table->text('attachment_url')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('occurred_at');
            $table->index(['category', 'occurred_at']);

            $table->foreign('created_by')
                  ->references('id_user')
                  ->on('tigaputri_md_users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
