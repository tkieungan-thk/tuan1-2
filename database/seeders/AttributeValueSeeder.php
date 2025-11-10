<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AttributeValue;
class AttributeValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attributeValues = [
            ['attribute_id' => 1, 'value' => 'Titan tự nhiên'],
            ['attribute_id' => 1, 'value' => 'Titan xanh'],
            ['attribute_id' => 1, 'value' => 'Titan trắng'],
            ['attribute_id' => 1, 'value' => 'Titan đen'],
            
            ['attribute_id' => 2, 'value' => '128GB'],
            ['attribute_id' => 2, 'value' => '256GB'],
            ['attribute_id' => 2, 'value' => '512GB'],
            ['attribute_id' => 2, 'value' => '1TB'],
            
            ['attribute_id' => 3, 'value' => 'Đen'],
            ['attribute_id' => 3, 'value' => 'Xám'],
            ['attribute_id' => 3, 'value' => 'Tím'],
            
            ['attribute_id' => 4, 'value' => '12GB'],
            
            ['attribute_id' => 5, 'value' => 'M3 8-core'],
            ['attribute_id' => 5, 'value' => 'M3 Pro 11-core'],
            
            ['attribute_id' => 6, 'value' => '8GB'],
            ['attribute_id' => 6, 'value' => '16GB'],
            ['attribute_id' => 6, 'value' => '24GB'],
            
            ['attribute_id' => 7, 'value' => '512GB'],
            ['attribute_id' => 7, 'value' => '1TB'],
            
            ['attribute_id' => 8, 'value' => 'Trắng'],
        ];

        foreach ($attributeValues as $value) {
            AttributeValue::create($value);
        }
    }
}
