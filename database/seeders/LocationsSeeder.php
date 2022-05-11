<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Location::create(['longitude'=>33.50856436506892,'latitude'=>36.267081179247334,'name'=>'المدينة الجامعية في المزة' ]);
        Location::create(['longitude'=>33.49749878013789,'latitude'=>36.316972846873846,'name'=>'المدينة الجامعية في الهمك' ]);
        Location::create(['longitude'=>33.54610698657646,'latitude'=>36.315330570563376,'name'=>'المدينة الجامعية في مساكن برزة' ]);
    }
}
