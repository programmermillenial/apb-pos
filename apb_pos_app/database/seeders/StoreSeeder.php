<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $store = Store::create([
            'name' => 'APB POS',
            'code' => 'APB',
            'owner_name' => 'Anggun Pribadi',
            'phone' => '081288853755',
            'address' => 'Indonesia',
            'tax_rate' => 11,
            'is_active' => true
        ]);
    }
}
