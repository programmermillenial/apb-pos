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

            User::updateOrCreate(
                ['username' => 'user_' . $code],
                [
                    'name'      => 'User ' . $outlet->name,
                    'username'  => 'user_' . $code,
                    'email'     => 'user_' . $code . '@apbpos.test',
                    'password'  => Hash::make('password'),
                    'role'      => 'manager',
                    'outlet_id' => $outlet->id,
                ]
            );
        }
    }
}
