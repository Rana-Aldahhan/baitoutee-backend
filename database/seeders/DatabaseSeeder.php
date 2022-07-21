<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            GlobalVariablesSeeder::class,
            LocationsSeeder::class,
            CategoriesSeeder::class,
            RolesSeeder::class,
            AdminsSeeder::class
        ]);
    }
}
