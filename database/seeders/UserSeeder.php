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
                'first_name' => 'Luigi',
                'last_name' => 'Butor',
                'phone_number' => '09616019985',
                'password' => Hash::make('password'),
                'username' => '@admin.luigi',
                'user_type' => 'admin',
                'first_logged_in' => false
            ]);


        User::create([
            'first_name' => 'Gene Mari Christopher',
            'last_name' => 'De La Rea',
            'phone_number' => '09165984951',
            'password' => Hash::make('password'),
            'username' => '@admin.jear',
            'user_type' => 'admin',
            'first_logged_in' => false
        ]);


        User::create([
            'first_name' => 'Giovanni',
            'last_name' => 'Marco',
            'phone_number' => '09972395348',
            'password' => Hash::make('password'),
            'username' => '@admin.gio',
            'user_type' => 'admin',
            'first_logged_in' => false
        ]);

        User::create([
            'first_name' => 'Michael',
            'last_name' => 'Lapuz',
            'phone_number' => '09214036746',
            'password' => Hash::make('password'),
            'username' => '@admin.michael',
            'user_type' => 'admin',
            'first_logged_in' => false
        ]);

        User::create([
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'phone_number' => '0977000000',
            'password' => Hash::make('password'),
            'username' => 'admin',
            'user_type' => 'admin',
            'first_logged_in' => false
        ]);
    }
}
