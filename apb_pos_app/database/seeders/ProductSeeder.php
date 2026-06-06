<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [

            // SMARTPHONE
            'Samsung Galaxy S24',
            'Samsung Galaxy A55',
            'Samsung Galaxy A35',
            'Xiaomi Redmi Note 13',
            'Xiaomi Redmi 13',
            'Xiaomi Poco X6',
            'Oppo Reno 12',
            'Oppo A79',
            'Vivo V30',
            'Vivo Y28',
            'Realme 13 Pro',
            'Realme C65',

            // TV
            'Samsung Smart TV 43"',
            'Samsung Smart TV 55"',
            'LG UHD TV 43"',
            'LG UHD TV 55"',
            'Sharp Aquos 50"',
            'Sharp Aquos 65"',
            'Sony Bravia 55"',
            'TCL Android TV 50"',

            // KULKAS
            'Samsung Kulkas 2 Pintu',
            'LG Kulkas Inverter',
            'Sharp Kulkas 2 Pintu',
            'Polytron Belleza',
            'Aqua Kulkas 1 Pintu',

            // AC
            'Samsung AC 1 PK',
            'Samsung AC 2 PK',
            'LG Dual Cool 1 PK',
            'Sharp AC Inverter 1 PK',
            'Polytron AC Deluxe',
            'Daikin AC 1 PK',

            // MONITOR
            'Samsung Monitor 24"',
            'Samsung Monitor 27"',
            'LG Monitor IPS 24"',
            'LG Ultrawide 29"',
            'Asus Gaming Monitor',
            'Acer Nitro Monitor',

            // LAPTOP
            'Asus Vivobook 14',
            'Asus TUF Gaming',
            'Acer Aspire 5',
            'Acer Nitro V15',
            'HP 14s',
            'HP Victus',

            // PRINTER
            'Epson L3210',
            'Epson L5290',
            'Canon G3010',
            'Canon G4770',

            // SPEAKER
            'Polytron Bluetooth Speaker',
            'Sony Soundbar',
            'LG Home Theater',

            // MESIN CUCI
            'Samsung Mesin Cuci Front Load',
            'LG Mesin Cuci Top Load',
        ];

        $outlets = Outlet::pluck('id')->toArray();
        $brands = Brand::pluck('id')->toArray();
        $units = Unit::pluck('id')->toArray();

        foreach ($products as $productName) {

            $categoryId = match (true) {
                str_contains(strtolower($productName), 'galaxy'),
                str_contains(strtolower($productName), 'redmi'),
                str_contains(strtolower($productName), 'oppo'),
                str_contains(strtolower($productName), 'vivo'),
                str_contains(strtolower($productName), 'realme')
                => ProductCategory::where('name', 'Smartphone')->value('id'),

                str_contains(strtolower($productName), 'tv')
                => ProductCategory::where('name', 'Televisi')->value('id'),

                str_contains(strtolower($productName), 'kulkas')
                => ProductCategory::where('name', 'Kulkas')->value('id'),

                str_contains(strtolower($productName), 'ac ')
                => ProductCategory::where('name', 'AC')->value('id'),

                str_contains(strtolower($productName), 'monitor')
                => ProductCategory::where('name', 'Monitor')->value('id'),

                str_contains(strtolower($productName), 'l3210'),
                str_contains(strtolower($productName), 'l5290'),
                str_contains(strtolower($productName), 'g3010'),
                str_contains(strtolower($productName), 'g4770')
                => ProductCategory::where('name', 'Printer')->value('id'),

                str_contains(strtolower($productName), 'speaker'),
                str_contains(strtolower($productName), 'soundbar'),
                str_contains(strtolower($productName), 'theater')
                => ProductCategory::where('name', 'Speaker')->value('id'),

                str_contains(strtolower($productName), 'mesin cuci')
                => ProductCategory::where('name', 'Mesin Cuci')->value('id'),

                default
                => ProductCategory::where('name', 'Laptop')->value('id'),
            };

            Product::create([
                'outlet_id' => $outlets[array_rand($outlets)],
                'product_category_id' => $categoryId,
                'brand_id' => $brands[array_rand($brands)],
                'unit_id' => $units[array_rand($units)],

                'sku' => 'SKU-' . strtoupper(fake()->unique()->bothify('#####')),
                'barcode' => fake()->unique()->ean13(),

                'name' => $productName,
                'slug' => Str::slug($productName),

                'description' => $productName,

                'cost_price' => rand(1000000, 15000000),
                'sell_price' => rand(2000000, 20000000),

                'stock' => rand(5, 100),
                'min_stock' => rand(1, 10),

                'weight' => rand(1, 20),

                'is_active' => 1,
            ]);
        }
    }
}
