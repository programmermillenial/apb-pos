<?php

namespace Database\Seeders;

use App\Models\Outlet;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Superadmin
        User::updateOrCreate(
            ['email' => 'superadmin@apbpos.test'],
            [
                'name' => 'Super Admin',
                'username'  => 'superadmin',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'outlet_id' => null,
            ]
        );

        $outlets = Outlet::orderBy('id')->get();

        foreach ($outlets as $outlet) {

            $code = strtolower($outlet->code);

            // Manager
            User::updateOrCreate(
                ['username' => 'manager_' . $code],
                [
                    'name'      => 'Manager ' . $outlet->name,
                    'username'  => 'manager_' . $code,
                    'email'     => 'manager_' . $code . '@apbpos.test',
                    'password'  => Hash::make('password'),
                    'role'      => 'manager',
                    'outlet_id' => $outlet->id,
                ]
            );

            // Cashier
            User::updateOrCreate(
                ['username' => 'cashier_' . $code],
                [
                    'name'      => 'Cashier ' . $outlet->name,
                    'username'  => 'cashier_' . $code,
                    'email'     => 'cashier_' . $code . '@apbpos.test',
                    'password'  => Hash::make('password'),
                    'role'      => 'cashier',
                    'outlet_id' => $outlet->id,
                ]
            );
        }
    }
}
