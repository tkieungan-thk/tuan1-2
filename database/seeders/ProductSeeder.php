<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Dế Mèn Phiêu Lưu Ký',
            'description' => 'Tác phẩm nổi tiếng của Tô Hoài, kể về cuộc phiêu lưu của Dế Mèn đầy ý nghĩa.',
            'price' => 55000,
            'stock' => 30,
        ]);
        
        Product::create([
            'name' => 'Tuổi Trẻ Đáng Giá Bao Nhiêu',
            'description' => 'Một cuốn sách truyền cảm hứng về định hướng cuộc sống cho người trẻ.',
            'price' => 78000,
            'stock' => 50,
        ]);
    }
}