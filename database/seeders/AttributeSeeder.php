<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Attribute;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attributes = [
            ['name' => 'Màu sắc', 'product_id' => 1],
            ['name' => 'Dung lượng', 'product_id' => 1],
            ['name' => 'Màu sắc', 'product_id' => 2],
            ['name' => 'RAM', 'product_id' => 2],
            ['name' => 'CPU', 'product_id' => 3],
            ['name' => 'RAM', 'product_id' => 3],
            ['name' => 'SSD', 'product_id' => 3],
            ['name' => 'Màu sắc', 'product_id' => 6],
        ];

        foreach ($attributes as $attribute) {
            Attribute::create($attribute);
        }
    }
}
