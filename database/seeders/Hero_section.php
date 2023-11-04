<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Hero;

class Hero_section extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Hero::create([
            'title' => '',
            'subtitle' => '',
            'description' => '',
            'image_path' => '',
        ]);
    }
}
