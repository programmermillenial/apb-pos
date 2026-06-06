<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Smartphone',
            'Televisi',
            'Kulkas',
            'AC',
            'Monitor',
            'Laptop',
            'Printer',
            'Speaker',
            'Mesin Cuci',
            'Aksesoris',
        ];

        foreach ($categories as $category) {
            ProductCategory::create([
                'name' => $category,
                'slug' => Str::slug($category),
                'description' => $category,
                'is_active' => 1,
            ]);
        }
    }
}
