<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

final class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \JsonException
     */
    public function run(): void
    {
        $json = file_get_contents(__DIR__ . '/source/products.json');

        $products = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        foreach ($products['products'] as $product) {
            Product::create([
                'sku' => $product['sku'],
                'name' => $product['name'],
                'category' => $product['category'],
                'price' => $product['price'],
            ]);
        }
    }
}
