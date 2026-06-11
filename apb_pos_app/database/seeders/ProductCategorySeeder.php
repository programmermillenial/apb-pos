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
            'Notebook',
            'Tablet',
        ];

        foreach ($categories as $category) {
            ProductCategory::updateOrCreate([
                'slug' => Str::slug($category),
            ], [
                'name' => $category,
                'description' => $category,
                'is_active' => 1,
            ]);
        }
    }
}
