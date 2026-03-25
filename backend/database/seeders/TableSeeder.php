<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Table\Infrastructure\Persistence\Models\EloquentTable;
use App\Zone\Infrastructure\Persistence\Models\EloquentZone;
use App\Restaurant\Infrastructure\Persistence\Models\EloquentRestaurant;

class TableSeeder extends Seeder
{
    
    public function run(): void
    {
        $salon = EloquentZone::where('name', 'Salón')->first();
        $azotea = EloquentZone::where('name', 'Azotea')->first();
        $terraza = EloquentZone::where('name', 'Terraza')->first();
        $restaurant = EloquentRestaurant::first();

        for ($i = 1; $i <= 10; $i++) {
            EloquentTable::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'zone_id' => $salon->id,
                'name' => 'Mesa ' . $i,
                'restaurant_id' => $restaurant->id,
            ]);
        }

        for ($i = 1; $i <= 10; $i++) {
            EloquentTable::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'zone_id' => $azotea->id,
                'name' => 'Azotea ' . $i,
                'restaurant_id' => $restaurant->id,
            ]);
        }

        for ($i = 1; $i <= 10; $i++) {
            EloquentTable::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'zone_id' => $terraza->id,
                'name' => 'Terraza ' . $i,
                'restaurant_id' => $restaurant->id,
            ]);
        }
    }
}
