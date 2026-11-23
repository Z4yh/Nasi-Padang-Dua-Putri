<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tigaputri_md_katering_order', function (Blueprint $table) {
            if (!Schema::hasColumn('tigaputri_md_katering_order', 'harga_satuan')) {
                $table->decimal('harga_satuan', 12, 2)->default(0)->after('total_boks_bonus');
            }
            if (!Schema::hasColumn('tigaputri_md_katering_order', 'ongkos_kirim')) {
                $table->decimal('ongkos_kirim', 12, 2)->default(0)->after('harga_satuan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tigaputri_md_katering_order', function (Blueprint $table) {
            foreach (['harga_satuan', 'ongkos_kirim'] as $col) {
                if (Schema::hasColumn('tigaputri_md_katering_order', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};