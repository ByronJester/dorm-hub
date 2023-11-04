<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            AboutUsSeeder::class,
            ContactUsSeeder::class,
            PrivacyPolicySeeder::class,
            TermsUserSeeder::class,
            Hero_section::class
        ]);
    }
}
