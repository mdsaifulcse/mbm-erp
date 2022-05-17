<?php

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


//        $this->call([
//            PermissionSeeder::class,
//            UserRolePermissionsSeeder::class,
//            MenuSeeder::class
//        ]);
        // $this->call(UserSeeder::class);

        // $this->call([
        //     PermissionSeeder::class,
        //     UserRolePermissionsSeeder::class,
        //     MenuSeeder::class
        // ]);
        // $this->call(UserSeeder::class);


        $this->call(HrDepartmentTableSeed::class);
    }

}
