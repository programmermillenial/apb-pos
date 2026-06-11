<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Outlet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            'Samsung',
            'Apple',
            'Asus',
            'Acer',
            'HP',
            'Dell',
            'Xiaomi',
            'Oppo',
            'Vivo',
        ];

        foreach ($brands as $brand) {
            Brand::updateOrCreate([
                'outlet_id' => null,
                'name' => $brand,
            ], [
                'slug'        => Str::slug($brand),
                'description' => $brand,
                'is_active' => 1,
            ]);
        }
    }
}
