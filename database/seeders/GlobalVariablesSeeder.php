<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GlobalVariablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         *global variables that are shared across the whole system:
         *cost_of_one_km
         *delivery_profit_percentage
         *meal_profit
         *balance
         *support_phone_number
         */
        DB::table('global_variables')->insert([
            'name' =>'cost_of_one_km',
            'value'=>'200'
        ]);
        DB::table('global_variables')->insert([
            'name' =>'delivery_profit_percentage',
            'value'=>'40'
        ]);
        DB::table('global_variables')->insert([
            'name' =>'meal_profit',
            'value'=>'1000'
        ]);
        DB::table('global_variables')->insert([
            'name' =>'balance',
            'value'=>'0'
        ]);
        DB::table('global_variables')->insert([
            'name' =>'support_phone_number',
            'value'=>'011-111-1111'
        ]);

    }
}
