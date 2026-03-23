<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Tax\Infrastructure\Persistence\Models\EloquentTax;

class TaxSeeder extends Seeder
{
    
    public function run(): void
    {
        EloquentTax::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => 'IVA Superreducido',
            'percentage' => 4,
        ]);

        EloquentTax::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => 'IVA Reducido',
            'percentage' => 10,
        ]);

        EloquentTax::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => 'IVA General',
            'percentage' => 21,
        ]);
    }
}
