<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'phone_number' => '0977000000',
            'password' => Hash::make('password'),
            'username' => 'admin1',
            'user_type' => 'admin',
            'first_logged_in' => false
        ]);


        User::create([
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'phone_number' => '0977000000',
            'password' => Hash::make('password'),
            'username' => 'admin2',
            'user_type' => 'admin',
            'first_logged_in' => false
        ]);


        User::create([
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'phone_number' => '0977000000',
            'password' => Hash::make('password'),
            'username' => 'admin3',
            'user_type' => 'admin',
            'first_logged_in' => false
        ]);

        User::create([
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'phone_number' => '0977000000',
            'password' => Hash::make('password'),
            'username' => 'admin4',
            'user_type' => 'admin',
            'first_logged_in' => false
        ]);
    }
}
