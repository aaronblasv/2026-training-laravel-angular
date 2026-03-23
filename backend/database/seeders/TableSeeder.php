<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Table\Infrastructure\Persistence\Models\EloquentTable;
use App\Zone\Infrastructure\Persistence\Models\EloquentZone;

class TableSeeder extends Seeder
{
    
    public function run(): void
    {
        $salon = EloquentZone::where('name', 'Salón')->first();

        for ($i = 1; $i <= 10; $i++) {
            EloquentTable::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'zone_id' => $salon->id,
                'name' => 'Mesa ' . $i,
            ]);
        }

        $azotea = EloquentZone::where('name', 'Azotea')->first();

        for ($i = 1; $i <= 10; $i++) {
            EloquentTable::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'zone_id' => $azotea->id,
                'name' => 'Azotea ' . $i,
            ]);
        }

        $terraza = EloquentZone::where('name', 'Terraza')->first();

        for ($i = 1; $i <= 10; $i++) {
            EloquentTable::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'zone_id' => $terraza->id,
                'name' => 'Terraza ' . $i,
            ]);
        }
    }
}
