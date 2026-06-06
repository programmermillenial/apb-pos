<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            [
                'name' => 'Piece',
                'short_name' => 'PCS',
            ],
            [
                'name' => 'Unit',
                'short_name' => 'UNIT',
            ],
            [
                'name' => 'Box',
                'short_name' => 'BOX',
            ],
            [
                'name' => 'Pack',
                'short_name' => 'PACK',
            ],
            [
                'name' => 'Kilogram',
                'short_name' => 'KG',
            ],
            [
                'name' => 'Gram',
                'short_name' => 'GR',
            ],
            [
                'name' => 'Liter',
                'short_name' => 'L',
            ],
            [
                'name' => 'Meter',
                'short_name' => 'M',
            ],
            [
                'name' => 'Centimeter',
                'short_name' => 'CM',
            ],
        ];

        foreach ($units as $unit) {
            Unit::create([
                'name' => $unit['name'],
                'slug'        => Str::slug($unit['name']),
                'short_name' => $unit['short_name'],
                'description' => $unit['name'],
                'is_active' => true,
            ]);
        }
    }
}
