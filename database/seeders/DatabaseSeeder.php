<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            StateSeeder::class,
            CitySeeder::class,
            ZipSeeder::class,
            AustinDemoSeeder::class,
        ]);
    }
}
