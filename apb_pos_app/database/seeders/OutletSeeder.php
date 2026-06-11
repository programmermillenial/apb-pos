<?php

namespace Database\Seeders;

use App\Models\Outlet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OutletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $outlets = [
            [
                'code' => 'JKT',
                'name' => 'APB Jakarta',
                'phone' => '021-1500101',
                'address' => 'Jakarta Pusat, DKI Jakarta',
                'latitude' => -6.2088000,
                'longitude' => 106.8456000,
            ],
            [
                'code' => 'BDG',
                'name' => 'APB Bandung',
                'phone' => '022-1500102',
                'address' => 'Bandung, Jawa Barat',
                'latitude' => -6.9175000,
                'longitude' => 107.6191000,
            ],
            [
                'code' => 'SBY',
                'name' => 'APB Surabaya',
                'phone' => '031-1500103',
                'address' => 'Surabaya, Jawa Timur',
                'latitude' => -7.2575000,
                'longitude' => 112.7521000,
            ],
            [
                'code' => 'MDN',
                'name' => 'APB Medan',
                'phone' => '061-1500104',
                'address' => 'Medan, Sumatera Utara',
                'latitude' => 3.5952000,
                'longitude' => 98.6722000,
            ],
            [
                'code' => 'MKS',
                'name' => 'APB Makassar',
                'phone' => '0411-1500105',
                'address' => 'Makassar, Sulawesi Selatan',
                'latitude' => -5.1477000,
                'longitude' => 119.4327000,
            ],
            [
                'code' => 'SMG',
                'name' => 'APB Semarang',
                'phone' => '024-1500106',
                'address' => 'Semarang, Jawa Tengah',
                'latitude' => -6.9667000,
                'longitude' => 110.4167000,
            ],
            [
                'code' => 'DPS',
                'name' => 'APB Denpasar',
                'phone' => '0361-1500107',
                'address' => 'Denpasar, Bali',
                'latitude' => -8.6705000,
                'longitude' => 115.2126000,
            ],
            [
                'code' => 'YGY',
                'name' => 'APB Yogyakarta',
                'phone' => '0274-1500108',
                'address' => 'Yogyakarta, DI Yogyakarta',
                'latitude' => -7.7956000,
                'longitude' => 110.3695000,
            ],
            [
                'code' => 'PLB',
                'name' => 'APB Palembang',
                'phone' => '0711-1500109',
                'address' => 'Palembang, Sumatera Selatan',
                'latitude' => -2.9761000,
                'longitude' => 104.7754000,
            ],
            [
                'code' => 'BPN',
                'name' => 'APB Balikpapan',
                'phone' => '0542-1500110',
                'address' => 'Balikpapan, Kalimantan Timur',
                'latitude' => -1.2379000,
                'longitude' => 116.8529000,
            ],
        ];

        foreach ($outlets as $outlet) {
            Outlet::updateOrCreate([
                'store_id' => 1,
                'code' => $outlet['code'],
            ], [
                'name' => $outlet['name'],
                'phone' => $outlet['phone'],
                'address' => $outlet['address'],
                'latitude' => $outlet['latitude'],
                'longitude' => $outlet['longitude'],
                'is_active' => 1,
            ]);
        }
    }
}
