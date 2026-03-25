<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Tax\Infrastructure\Persistence\Models\EloquentTax;
use App\Restaurant\Infrastructure\Persistence\Models\EloquentRestaurant;

class TaxSeeder extends Seeder
{
    
    public function run(): void
    {

        $restaurant = EloquentRestaurant::first();

        EloquentTax::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => 'IVA Superreducido',
            'percentage' => 4,
            'restaurant_id' => $restaurant->id,
        ]);

        EloquentTax::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => 'IVA Reducido',
            'percentage' => 10,
            'restaurant_id' => $restaurant->id,
        ]);

        EloquentTax::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => 'IVA General',
            'percentage' => 21,
            'restaurant_id' => $restaurant->id,
        ]);
    }
}
