<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductOutlet;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $outlets = Outlet::where('is_active', 1)->orderBy('id')->get();
        $defaultOutletId = $outlets->first()?->id;
        $unitId = Unit::where('short_name', 'UNIT')->value('id') ?? Unit::where('short_name', 'PCS')->value('id') ?? Unit::value('id');

        if (!$defaultOutletId || !$unitId) {
            return;
        }

        $categories = ProductCategory::pluck('id', 'name');
        $brands = Brand::pluck('id', 'name');

        $products = [
            ['Samsung', 'Smartphone', 'Galaxy S24 Ultra 12/256GB', 21999000],
            ['Samsung', 'Smartphone', 'Galaxy S24 Plus 12/256GB', 17999000],
            ['Samsung', 'Smartphone', 'Galaxy S24 8/256GB', 13999000],
            ['Samsung', 'Smartphone', 'Galaxy S23 FE 8/256GB', 8999000],
            ['Samsung', 'Smartphone', 'Galaxy Z Fold6 12/256GB', 26499000],
            ['Samsung', 'Smartphone', 'Galaxy Z Flip6 12/256GB', 17499000],
            ['Samsung', 'Smartphone', 'Galaxy A55 5G 8/256GB', 6299000],
            ['Samsung', 'Smartphone', 'Galaxy A35 5G 8/256GB', 4999000],
            ['Samsung', 'Smartphone', 'Galaxy A25 5G 8/256GB', 3999000],
            ['Samsung', 'Smartphone', 'Galaxy A15 8/256GB', 2999000],
            ['Apple', 'Smartphone', 'iPhone 15 Pro Max 256GB', 24999000],
            ['Apple', 'Smartphone', 'iPhone 15 Pro 128GB', 19999000],
            ['Apple', 'Smartphone', 'iPhone 15 Plus 128GB', 16499000],
            ['Apple', 'Smartphone', 'iPhone 15 128GB', 13999000],
            ['Apple', 'Smartphone', 'iPhone 14 128GB', 11999000],
            ['Apple', 'Smartphone', 'iPhone 13 128GB', 9999000],
            ['Xiaomi', 'Smartphone', '14 Ultra 16/512GB', 16999000],
            ['Xiaomi', 'Smartphone', '14 12/256GB', 11999000],
            ['Xiaomi', 'Smartphone', '13T 12/256GB', 6499000],
            ['Xiaomi', 'Smartphone', 'Redmi Note 13 Pro Plus 5G 12/512GB', 5999000],
            ['Xiaomi', 'Smartphone', 'Redmi Note 13 Pro 5G 8/256GB', 4399000],
            ['Xiaomi', 'Smartphone', 'Redmi Note 13 8/256GB', 2799000],
            ['Xiaomi', 'Smartphone', 'Redmi 13C 8/256GB', 1999000],
            ['Xiaomi', 'Smartphone', 'Poco F6 12/512GB', 5899000],
            ['Xiaomi', 'Smartphone', 'Poco X6 Pro 12/512GB', 4999000],
            ['Oppo', 'Smartphone', 'Find X8 12/256GB', 13999000],
            ['Oppo', 'Smartphone', 'Reno12 Pro 5G 12/512GB', 8999000],
            ['Oppo', 'Smartphone', 'Reno12 5G 12/256GB', 6999000],
            ['Oppo', 'Smartphone', 'Reno11 F 5G 8/256GB', 4599000],
            ['Oppo', 'Smartphone', 'A98 5G 8/256GB', 4299000],
            ['Oppo', 'Smartphone', 'A79 5G 8/256GB', 3699000],
            ['Oppo', 'Smartphone', 'A60 8/128GB', 2599000],
            ['Oppo', 'Smartphone', 'A18 4/128GB', 1799000],
            ['Vivo', 'Smartphone', 'X100 Pro 16/512GB', 16999000],
            ['Vivo', 'Smartphone', 'V40 5G 12/512GB', 6999000],
            ['Vivo', 'Smartphone', 'V30 Pro 12/512GB', 8999000],
            ['Vivo', 'Smartphone', 'V30 12/256GB', 5999000],
            ['Vivo', 'Smartphone', 'V29 12/512GB', 6499000],
            ['Vivo', 'Smartphone', 'Y100 5G 8/256GB', 3899000],
            ['Vivo', 'Smartphone', 'Y28 8/256GB', 2799000],
            ['Vivo', 'Smartphone', 'Y18 6/128GB', 1999000],
            ['Asus', 'Smartphone', 'ROG Phone 8 Pro 16/512GB', 16999000],
            ['Asus', 'Smartphone', 'ROG Phone 8 12/256GB', 12999000],
            ['Asus', 'Smartphone', 'Zenfone 11 Ultra 12/256GB', 10999000],
            ['Asus', 'Smartphone', 'Zenfone 10 8/256GB', 8999000],

            ['Asus', 'Notebook', 'Vivobook 14 A1404ZA i3 8/512GB', 6999000],
            ['Asus', 'Notebook', 'Vivobook 14 A1404VA i5 8/512GB', 8999000],
            ['Asus', 'Notebook', 'Vivobook 15 OLED K3504VA i5 16/512GB', 11999000],
            ['Asus', 'Notebook', 'Zenbook 14 OLED UX3405MA Ultra 7 16/1TB', 18999000],
            ['Asus', 'Notebook', 'Zenbook S 13 OLED UX5304VA i7 16/1TB', 21999000],
            ['Asus', 'Notebook', 'TUF Gaming A15 Ryzen 7 RTX 4050 16/512GB', 15999000],
            ['Asus', 'Notebook', 'TUF Gaming F15 i7 RTX 4060 16/1TB', 19999000],
            ['Asus', 'Notebook', 'ROG Zephyrus G14 Ryzen 9 RTX 4060 16/1TB', 29999000],
            ['Asus', 'Notebook', 'ROG Strix G16 i9 RTX 4070 16/1TB', 34999000],
            ['Asus', 'Notebook', 'ExpertBook B1 B1402CVA i5 8/512GB', 9999000],
            ['Acer', 'Notebook', 'Aspire 3 A314 Ryzen 3 8/512GB', 5799000],
            ['Acer', 'Notebook', 'Aspire 5 A514 i5 8/512GB', 8499000],
            ['Acer', 'Notebook', 'Aspire 7 A715 Ryzen 5 RTX 3050 16/512GB', 11999000],
            ['Acer', 'Notebook', 'Swift Go 14 OLED Ultra 5 16/512GB', 13999000],
            ['Acer', 'Notebook', 'Swift X 14 i7 RTX 4050 16/1TB', 21999000],
            ['Acer', 'Notebook', 'Nitro V 15 i5 RTX 4050 16/512GB', 13999000],
            ['Acer', 'Notebook', 'Nitro 16 Ryzen 7 RTX 4060 16/1TB', 19999000],
            ['Acer', 'Notebook', 'Predator Helios Neo 16 i7 RTX 4060 16/1TB', 23999000],
            ['Acer', 'Notebook', 'TravelMate P2 i5 8/512GB', 9999000],
            ['Acer', 'Notebook', 'Chromebook Spin 314 8/128GB', 5999000],
            ['HP', 'Notebook', '14s dq5115TU i3 8/512GB', 6499000],
            ['HP', 'Notebook', '14s em0014AU Ryzen 5 8/512GB', 7499000],
            ['HP', 'Notebook', 'Pavilion Aero 13 Ryzen 5 16/512GB', 12999000],
            ['HP', 'Notebook', 'Pavilion Plus 14 OLED Ultra 5 16/512GB', 14999000],
            ['HP', 'Notebook', 'Envy x360 14 i7 16/1TB', 19999000],
            ['HP', 'Notebook', 'Victus 15 Ryzen 5 RTX 3050 16/512GB', 12999000],
            ['HP', 'Notebook', 'Victus 16 Ryzen 7 RTX 4060 16/1TB', 18999000],
            ['HP', 'Notebook', 'Omen 16 i7 RTX 4060 16/1TB', 24999000],
            ['HP', 'Notebook', 'ProBook 440 G10 i5 8/512GB', 11999000],
            ['HP', 'Notebook', 'EliteBook 840 G10 i7 16/1TB', 22999000],
            ['Dell', 'Notebook', 'Inspiron 14 5430 i5 8/512GB', 9999000],
            ['Dell', 'Notebook', 'Inspiron 15 3530 i5 8/512GB', 8999000],
            ['Dell', 'Notebook', 'Vostro 3420 i3 8/512GB', 6999000],
            ['Dell', 'Notebook', 'Vostro 3520 i5 8/512GB', 8499000],
            ['Dell', 'Notebook', 'XPS 13 Plus i7 16/1TB', 27999000],
            ['Dell', 'Notebook', 'XPS 15 i7 RTX 4050 16/1TB', 34999000],
            ['Dell', 'Notebook', 'G15 5530 i7 RTX 4060 16/1TB', 21999000],
            ['Dell', 'Notebook', 'Latitude 3440 i5 8/512GB', 12999000],
            ['Dell', 'Notebook', 'Latitude 5440 i7 16/512GB', 18999000],
            ['Dell', 'Notebook', 'Alienware m16 R2 Ultra 9 RTX 4070 32/1TB', 42999000],
            ['Apple', 'Notebook', 'MacBook Air 13 M2 8/256GB', 15999000],
            ['Apple', 'Notebook', 'MacBook Air 13 M3 8/256GB', 17999000],
            ['Apple', 'Notebook', 'MacBook Air 15 M3 8/256GB', 21999000],
            ['Apple', 'Notebook', 'MacBook Pro 14 M3 8/512GB', 27999000],
            ['Apple', 'Notebook', 'MacBook Pro 14 M3 Pro 18/512GB', 34999000],

            ['Samsung', 'Tablet', 'Galaxy Tab S9 FE 6/128GB', 6499000],
            ['Samsung', 'Tablet', 'Galaxy Tab S9 8/128GB', 11999000],
            ['Samsung', 'Tablet', 'Galaxy Tab S9 Ultra 12/256GB', 19999000],
            ['Apple', 'Tablet', 'iPad 10th Gen WiFi 64GB', 6999000],
            ['Apple', 'Tablet', 'iPad Air 11 M2 WiFi 128GB', 11999000],
            ['Apple', 'Tablet', 'iPad Pro 11 M4 WiFi 256GB', 17999000],
            ['Xiaomi', 'Tablet', 'Pad 6 8/256GB', 4999000],
            ['Xiaomi', 'Tablet', 'Pad 6S Pro 12.4 8/256GB', 7999000],
            ['Oppo', 'Tablet', 'Pad Air 4/64GB', 3999000],
            ['Vivo', 'Tablet', 'Pad Air 8/128GB', 5499000],
        ];

        foreach ($products as $index => [$brandName, $categoryName, $name, $sellPrice]) {
            $brandId = $brands[$brandName] ?? null;
            $categoryId = $categories[$categoryName] ?? null;

            if (!$brandId || !$categoryId) {
                continue;
            }

            $sku = $this->buildSku($brandName, $categoryName, $index + 1);
            $payload = [
                'product_category_id' => $categoryId,
                'brand_id' => $brandId,
                'unit_id' => $unitId,
                'barcode' => $this->buildBarcode($index + 1),
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => "{$name} - {$categoryName} {$brandName}",
                'cost_price' => $this->costPrice($sellPrice),
                'sell_price' => $sellPrice,
                'min_stock' => 2,
                'weight' => $categoryName === 'Notebook' ? 2.00 : ($categoryName === 'Tablet' ? 0.60 : 0.25),
                'is_active' => 1,
            ];

            if (Schema::hasColumn('products', 'stock')) {
                $payload['stock'] = 0;
            }

            $product = Product::updateOrCreate(['sku' => $sku], $payload);

            foreach ($outlets as $outlet) {
                ProductOutlet::updateOrCreate([
                    'product_id' => $product->id,
                    'outlet_id' => $outlet->id,
                ], [
                    'stock' => 0,
                    'reorder_point' => 2,
                ]);
            }
        }
    }

    private function buildSku(string $brand, string $category, int $number): string
    {
        $brandCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $brand), 0, 3));
        $categoryCode = match ($category) {
            'Smartphone' => 'PHN',
            'Notebook' => 'NTB',
            'Tablet' => 'TAB',
            default => 'PRD',
        };

        return "{$brandCode}-{$categoryCode}-" . str_pad((string) $number, 4, '0', STR_PAD_LEFT);
    }

    private function buildBarcode(int $number): string
    {
        return '899' . str_pad((string) $number, 10, '0', STR_PAD_LEFT);
    }

    private function costPrice(int $sellPrice): int
    {
        return (int) (floor(($sellPrice * 0.9) / 1000) * 1000);
    }
}
