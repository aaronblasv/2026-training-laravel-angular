<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Zone\Infrastructure\Persistence\Models\EloquentZone;
use App\Restaurant\Infrastructure\Persistence\Models\EloquentRestaurant;

class ZoneSeeder extends Seeder
{
    
    public function run(): void
    {

        $restaurant = EloquentRestaurant::first();

        EloquentZone::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => 'Salón',
            'restaurant_id' => $restaurant->id,
        ]);

        EloquentZone::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => 'Azotea',
            'restaurant_id' => $restaurant->id,
        ]);

        EloquentZone::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => 'Terraza',
            'restaurant_id' => $restaurant->id,
        ]);
    }
}
