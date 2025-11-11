<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Điện thoại',
                'description' => 'Các dòng điện thoại thông minh',
            ],
            [
                'name'        => 'Laptop',
                'description' => 'Máy tính xách tay các hãng',
            ],
            [
                'name'        => 'Tablet',
                'description' => 'Máy tính bảng',
            ],
            [
                'name'        => 'Phụ kiện',
                'description' => 'Phụ kiện điện tử',
            ],
            [
                'name'        => 'Đồng hồ thông minh',
                'description' => 'Smartwatch và wearable devices',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
