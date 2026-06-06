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
            'LG',
            'Sharp',
            'Polytron',
            'Sony',
            'TCL',
            'Xiaomi',
            'Oppo',
            'Vivo',
            'Realme',
            'Asus',
            'Acer',
            'HP',
            'Canon',
            'Epson',
        ];

        foreach ($brands as $brand) {
            Brand::create([
                'name' => $brand,
                'slug'        => Str::slug($brand),
                'description' => $brand,
                'is_active' => 1,
            ]);
        }
    }
}
