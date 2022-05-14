<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create(['name'=>'super admin' ]);
        Role::create(['name'=>'hr admin' ]);
        Role::create(['name'=>'accountant admin' ]);
        Role::create(['name'=>'orders admin' ]);
        Role::create(['name'=>'reports admin' ]);
    }
}
