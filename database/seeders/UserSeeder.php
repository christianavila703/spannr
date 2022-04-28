<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superAdmins = [
            [
                'name' => 'Sydney',
                'email' => 'sydney@spannr.com',
                'password' => Hash::make('sydneyisgreat'),
            ]
        ];

        $adminUsers = [
            [
                'name' => 'Brett',
                'email' => 'brett@spannr.com',
                'password' => Hash::make('youneedtochangethepassword'),
            ],
            [
                'name' => 'Brian',
                'email' => 'brian@spannr.com',
                'password' => Hash::make('youneedtochangethepassword'),
            ],
            [
                'name' => 'Cody',
                'email' => 'cody@spannr.com',
                'password' => Hash::make('youneedtochangethepassword'),
            ],
            [
                'name' => 'Dillon',
                'email' => 'dillon@spannr.com',
                'password' => Hash::make('youneedtochangethepassword'),
            ],
            [
                'name' => 'Joshua',
                'email' => 'joshua@spannr.com',
                'password' => Hash::make('youneedtochangethepassword'),
            ],
            [
                'name' => 'Luke',
                'email' => 'luke@spannr.com',
                'password' => Hash::make('youneedtochangethepassword'),
            ],
        ];

        foreach ($superAdmins as $adminUser) {
            $user = User::create($adminUser);
            $user->assignRole('Super Admin');
        }

        foreach ($adminUsers as $adminUser) {
            $user = User::create($adminUser);
            $user->assignRole('Admin');
        }
    }
}
