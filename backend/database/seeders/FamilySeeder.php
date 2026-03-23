<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Family\Infrastructure\Persistence\Models\EloquentFamily;

class FamilySeeder extends Seeder
{
    
    public function run(): void
    {
        EloquentFamily::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => 'Entrantes',
            'active' => true,
        ]);

        EloquentFamily::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => 'Platos Principales',
            'active' => true,
        ]);

        EloquentFamily::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => 'Acompañantes',
            'active' => true,
        ]);

        EloquentFamily::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => 'Postres',
            'active' => true,
        ]);

        EloquentFamily::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => 'Bebidas',
            'active' => true,
        ]);
    }
}
