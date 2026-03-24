<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Product\Infrastructure\Persistence\Models\EloquentProduct;
use App\Family\Infrastructure\Persistence\Models\EloquentFamily;
use App\Tax\Infrastructure\Persistence\Models\EloquentTax;

class ProductSeeder extends Seeder
{
    public function run(): void
    {

        $products =[
            ['name' => 'Ensalada Valenciana', 'family' => 'Entrantes', 'tax' => 'IVA Reducido', 'price' => 850],
            ['name' => 'Ensalada Griega', 'family' => 'Entrantes', 'tax' => 'IVA Reducido', 'price' => 800],
            ['name' => 'Croquetas de Jamón', 'family' => 'Entrantes', 'tax' => 'IVA Reducido', 'price' => 650],
            ['name' => 'Croquetas de Pollo', 'family' => 'Entrantes', 'tax' => 'IVA Reducido', 'price' => 650],
            ['name' => 'Patatas Bravas', 'family' => 'Entrantes', 'tax' => 'IVA Reducido', 'price' => 700],
            ['name' => 'Pollo Empanado', 'family' => 'Platos Principales', 'tax' => 'IVA Reducido', 'price' => 1200],
            ['name' => 'Lasaña de Carne', 'family' => 'Platos Principales', 'tax' => 'IVA Reducido', 'price' => 1300],
            ['name' => 'Lasaña Vegetariana', 'family' => 'Platos Principales', 'tax' => 'IVA Reducido', 'price' => 1250],
            ['name' => 'Pizza Margarita', 'family' => 'Platos Principales', 'tax' => 'IVA Reducido', 'price' => 1100],
            ['name' => 'Pizza Pepperoni', 'family' => 'Platos Principales', 'tax' => 'IVA Reducido', 'price' => 1150],
            ['name' => 'Patatas Fritas', 'family' => 'Acompañantes', 'tax' => 'IVA Reducido', 'price' => 500],
            ['name' => 'Ensalada Mixta', 'family' => 'Acompañantes', 'tax' => 'IVA Reducido', 'price' => 600],
            ['name' => 'Arroz Blanco', 'family' => 'Acompañantes', 'tax' => 'IVA Reducido', 'price' => 550],
            ['name' => 'Helado de Vainilla', 'family' => 'Postres', 'tax' => 'IVA Reducido', 'price' => 400],
            ['name' => 'Helado de Chocolate', 'family' => 'Postres', 'tax' => 'IVA Reducido', 'price' => 400],
            ['name' => 'Tarta de Queso', 'family' => 'Postres', 'tax' => 'IVA Reducido', 'price' => 450],
            ['name' => 'Tarta de Chocolate', 'family' => 'Postres', 'tax' => 'IVA Reducido', 'price' => 450],
            ['name' => 'Coca-Cola', 'family' => 'Bebidas', 'tax' => 'IVA General', 'price' => 300],
            ['name' => 'Fanta Naranja', 'family' => 'Bebidas', 'tax' => 'IVA General', 'price' => 300],
            ['name' => 'Agua Mineral', 'family' => 'Bebidas', 'tax' => 'IVA General', 'price' => 250],
        ];

        foreach ($products as $product) {
            $family = EloquentFamily::where ('name', $product['family'])->first();
            $tax = EloquentTax::where ('name', $product['tax'])->first();

            EloquentProduct::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'family_id' => $family->id,
                'tax_id' => $tax->id,
                'name' => $product['name'],
                'price' => $product['price'],
                'stock' => 50,
                'active' => true
            ]);
        }
    }
}