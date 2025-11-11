<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name'        => 'iPhone 15 Pro',
                'category_id' => 1,
                'description' => 'iPhone 15 Pro với chip A17 Pro',
                'price'       => 25990000,
                'stock'       => 50,
                'status'      => 'active',
            ],
            [
                'name'        => 'Samsung Galaxy S24 Ultra',
                'category_id' => 1,
                'description' => 'Flagship Samsung với bút S-Pen',
                'price'       => 28990000,
                'stock'       => 30,
                'status'      => 'active',
            ],
            [
                'name'        => 'MacBook Pro 14 inch',
                'category_id' => 2,
                'description' => 'MacBook Pro với chip M3',
                'price'       => 45990000,
                'stock'       => 20,
                'status'      => 'active',
            ],
            [
                'name'        => 'Dell XPS 13',
                'category_id' => 2,
                'description' => 'Laptop cao cấp Dell XPS series',
                'price'       => 32990000,
                'stock'       => 15,
                'status'      => 'active',
            ],
            [
                'name'        => 'iPad Air 5',
                'category_id' => 3,
                'description' => 'iPad Air thế hệ thứ 5',
                'price'       => 17990000,
                'stock'       => 25,
                'status'      => 'active',
            ],
            [
                'name'        => 'AirPods Pro',
                'category_id' => 4,
                'description' => 'Tai nghe không dây Apple',
                'price'       => 5990000,
                'stock'       => 100,
                'status'      => 'active',
            ],
            [
                'name'        => 'Apple Watch Series 9',
                'category_id' => 5,
                'description' => 'Đồng hồ thông minh Apple',
                'price'       => 11990000,
                'stock'       => 40,
                'status'      => 'active',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
