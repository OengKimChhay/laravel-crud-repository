<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\AuthSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\CategorySeeder;
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
            AuthSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}
