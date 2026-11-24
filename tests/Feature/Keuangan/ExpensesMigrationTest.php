<?php

namespace Tests\Feature\Keuangan;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ExpensesMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_expenses_table_and_indexes_exist(): void
    {
        $this->assertTrue(Schema::hasTable('expenses'));

        $this->assertTrue(Schema::hasColumns('expenses', [
            'id',
            'category',
            'amount',
            'note',
            'fund_source',
            'occurred_at',
            'attachment_url',
            'created_by',
            'created_at',
            'updated_at',
            'deleted_at',
        ]));
    }

    public function test_migration_can_be_rolled_back_and_re_run(): void
    {
        // Rollback migrasi terakhir
        $this->artisan('migrate:rollback', ['--step' => 1])->assertSuccessful();
        $this->assertFalse(Schema::hasTable('expenses'));

        // Jalankan migrasi lagi
        $this->artisan('migrate')->assertSuccessful();
        $this->assertTrue(Schema::hasTable('expenses'));
    }
}
