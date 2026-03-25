<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Family\Infrastructure\Persistence\Models\EloquentFamily;
use App\Restaurant\Infrastructure\Persistence\Models\EloquentRestaurant;

class FamilySeeder extends Seeder
{
    
    public function run(): void
    {

        $restaurant = EloquentRestaurant::first();

        EloquentFamily::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => 'Entrantes',
            'active' => true,
            'restaurant_id' => $restaurant->id,
        ]);

        EloquentFamily::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => 'Platos Principales',
            'active' => true,
            'restaurant_id' => $restaurant->id,
        ]);

        EloquentFamily::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => 'Acompañantes',
            'active' => true,
            'restaurant_id' => $restaurant->id,
        ]);

        EloquentFamily::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => 'Postres',
            'active' => true,
            'restaurant_id' => $restaurant->id,
        ]);

        EloquentFamily::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => 'Bebidas',
            'active' => true,
            'restaurant_id' => $restaurant->id,
        ]);
    }
}
