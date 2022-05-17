<?php

use Illuminate\Database\Seeder;
use App\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserRolePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $developer = User::create([
            'name' => 'Md.Saiful Islam',
            'email' => 'developer@developer.com',
            //'username' => 'developer',
            'mobile' => '01829655974',
            'password' => bcrypt('12345678')
        ]);

        $developerRole = Role::create([
            'name' => 'developer'
        ]);

        $permissions = Permission::pluck('id', 'id')->all();

        $developerRole->syncPermissions($permissions);

        $developer->assignRole([$developerRole->id]);

        // -----------------------------

        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            //'username' => 'admin',
            'mobile' => '01999999999',
            'password' => bcrypt('12345678')
        ]);

        $role = Role::create([
            'name' => 'admin'
        ]);


        $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);
    }
}
