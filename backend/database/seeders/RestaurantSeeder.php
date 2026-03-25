<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Restaurant\Infrastructure\Persistence\Models\EloquentRestaurant;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurants = [
            ['name' => 'Restaurante Paco', 'legal_name' => 'Restaurante Paco S.A.', 'tax_id' => '123456789', 'email' => 'restaurante@paco.com'],
            ['name' => 'Restaurante Juan', 'legal_name' => 'Restaurante Juan S.A.', 'tax_id' => '987654321', 'email' => 'restaurante@juan.com'],
            ['name' => 'Restaurante Maria', 'legal_name' => 'Restaurante Maria S.A.', 'tax_id' => '456789123', 'email' => 'restaurante@maria.com']
        ];

        foreach ($restaurants as $restaurant) {
            EloquentRestaurant::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => $restaurant['name'],
                'legal_name' => $restaurant['legal_name'],
                'tax_id' => $restaurant['tax_id'],
                'email' => $restaurant['email'],
                'password' => \Illuminate\Support\Facades\Hash::make('password')
            ]);
        }
    }
}
