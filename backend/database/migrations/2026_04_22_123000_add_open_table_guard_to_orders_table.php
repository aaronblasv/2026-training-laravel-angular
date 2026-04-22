<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('open_table_guard')
                ->nullable()
                ->virtualAs("CASE WHEN status = 'open' THEN table_id END")
                ->after('table_id');

            $table->unique(['restaurant_id', 'open_table_guard'], 'uniq_open_table_per_restaurant');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique('uniq_open_table_per_restaurant');
            $table->dropColumn('open_table_guard');
        });
    }
};