<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_ticket_counters', function (Blueprint $table) {
            $table->foreignId('restaurant_id')->primary()->constrained('restaurants')->cascadeOnDelete();
            $table->unsignedInteger('last_ticket_number')->default(0);
            $table->timestamps();
        });

        $now = now();

        $rows = DB::table('sales')
            ->selectRaw('restaurant_id, COALESCE(MAX(ticket_number), 0) AS last_ticket_number')
            ->groupBy('restaurant_id')
            ->get()
            ->map(fn (object $row) => [
                'restaurant_id' => $row->restaurant_id,
                'last_ticket_number' => (int) $row->last_ticket_number,
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->all();

        if ($rows !== []) {
            DB::table('restaurant_ticket_counters')->insert($rows);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_ticket_counters');
    }
};