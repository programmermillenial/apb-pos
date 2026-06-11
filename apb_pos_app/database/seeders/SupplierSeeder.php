<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'code' => 'SUP-ERA',
                'name' => 'PT Erajaya Swasembada Tbk',
                'phone' => '021-80667777',
                'email' => 'sales@erajaya.test',
                'address' => 'Jakarta Barat, DKI Jakarta',
                'pic_name' => 'Budi Santoso',
                'pic_phone' => '0812-1000-0001',
            ],
            [
                'code' => 'SUP-TAM',
                'name' => 'PT Teletama Artha Mandiri',
                'phone' => '021-29557788',
                'email' => 'sales@tam.test',
                'address' => 'Jakarta Barat, DKI Jakarta',
                'pic_name' => 'Rina Wijaya',
                'pic_phone' => '0812-1000-0002',
            ],
            [
                'code' => 'SUP-MTD',
                'name' => 'PT Synnex Metrodata Indonesia',
                'phone' => '021-29345800',
                'email' => 'sales@synnexmetrodata.test',
                'address' => 'Jakarta Barat, DKI Jakarta',
                'pic_name' => 'Andi Pratama',
                'pic_phone' => '0812-1000-0003',
            ],
            [
                'code' => 'SUP-DTS',
                'name' => 'PT Datascrip',
                'phone' => '021-6544515',
                'email' => 'sales@datascrip.test',
                'address' => 'Jakarta Pusat, DKI Jakarta',
                'pic_name' => 'Maya Lestari',
                'pic_phone' => '0812-1000-0004',
            ],
            [
                'code' => 'SUP-ECS',
                'name' => 'PT ECS Indo Jaya',
                'phone' => '021-30051234',
                'email' => 'sales@ecsindojaya.test',
                'address' => 'Jakarta Selatan, DKI Jakarta',
                'pic_name' => 'Dedi Kurniawan',
                'pic_phone' => '0812-1000-0005',
            ],
            [
                'code' => 'SUP-AXI',
                'name' => 'PT Axindo Infotama',
                'phone' => '021-53663333',
                'email' => 'sales@axindo.test',
                'address' => 'Jakarta Barat, DKI Jakarta',
                'pic_name' => 'Nadia Putri',
                'pic_phone' => '0812-1000-0006',
            ],
            [
                'code' => 'SUP-AST',
                'name' => 'PT Astrindo Senayasa',
                'phone' => '021-6505555',
                'email' => 'sales@astrindo.test',
                'address' => 'Jakarta Utara, DKI Jakarta',
                'pic_name' => 'Agus Firmansyah',
                'pic_phone' => '0812-1000-0007',
            ],
            [
                'code' => 'SUP-ING',
                'name' => 'PT Ingram Micro Indonesia',
                'phone' => '021-29345678',
                'email' => 'sales@ingrammicro.test',
                'address' => 'Jakarta Selatan, DKI Jakarta',
                'pic_name' => 'Sari Permata',
                'pic_phone' => '0812-1000-0008',
            ],
            [
                'code' => 'SUP-HRS',
                'name' => 'PT Harrisma Informatika Jaya',
                'phone' => '021-56958888',
                'email' => 'sales@harrisma.test',
                'address' => 'Jakarta Barat, DKI Jakarta',
                'pic_name' => 'Yoga Saputra',
                'pic_phone' => '0812-1000-0009',
            ],
            [
                'code' => 'SUP-TRK',
                'name' => 'PT Trikomsel Oke Tbk',
                'phone' => '021-30005555',
                'email' => 'sales@trikomsel.test',
                'address' => 'Jakarta Selatan, DKI Jakarta',
                'pic_name' => 'Intan Maharani',
                'pic_phone' => '0812-1000-0010',
            ],
            [
                'code' => 'SUP-BEC',
                'name' => 'PT Berca Cakra Teknologi',
                'phone' => '021-3905500',
                'email' => 'sales@berca.test',
                'address' => 'Jakarta Pusat, DKI Jakarta',
                'pic_name' => 'Fajar Nugroho',
                'pic_phone' => '0812-1000-0011',
            ],
            [
                'code' => 'SUP-PAS',
                'name' => 'PT Pazia Pillar Mercycom',
                'phone' => '021-62308888',
                'email' => 'sales@pazia.test',
                'address' => 'Jakarta Utara, DKI Jakarta',
                'pic_name' => 'Lia Anggraini',
                'pic_phone' => '0812-1000-0012',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::updateOrCreate([
                'code' => $supplier['code'],
            ], [
                'name' => $supplier['name'],
                'phone' => $supplier['phone'],
                'email' => $supplier['email'],
                'address' => $supplier['address'],
                'pic_name' => $supplier['pic_name'],
                'pic_phone' => $supplier['pic_phone'],
                'is_active' => 1,
            ]);
        }
    }
}
