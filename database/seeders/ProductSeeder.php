<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            ['name' => 'Monitor de incidentes'],
            ['name' => 'Produto 1'],
            ['name' => 'Produto 2'],
            // Adicione mais produtos conforme necessário
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
