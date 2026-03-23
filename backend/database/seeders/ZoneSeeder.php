<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Zone\Infrastructure\Persistence\Models\EloquentZone;

class ZoneSeeder extends Seeder
{
    
    public function run(): void
    {
        EloquentZone::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => 'Salón'
        ]);

        EloquentZone::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => 'Azotea'
        ]);

        EloquentZone::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => 'Terraza'
        ]);
    }
}
