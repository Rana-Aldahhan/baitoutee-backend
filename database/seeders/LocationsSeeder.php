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
        Location::create(['longitude'=>36.267081179247334,'latitude'=>33.50856436506892,'name'=>'المدينة الجامعية في المزة' ]);
        Location::create(['longitude'=>36.316972846873846,'latitude'=>33.49749878013789,'name'=>'المدينة الجامعية في الهمك' ]);
        Location::create(['longitude'=>36.315330570563376,'latitude'=>33.54610698657646,'name'=>'المدينة الجامعية في مساكن برزة' ]);
        Location::create(['longitude'=>36.301420969717896,'latitude'=>33.53764712839549,'name'=>'ركن الدين-صلاح الدين',
        'distance_to_first_location'=>'4.53','distance_to_second_location'=>'4.69','distance_to_third_location'=>'2.19']);
        
    }
}
