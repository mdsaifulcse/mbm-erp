<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [

            'role-list',
            'role-create',
            'role-edit',
            'role-delete',

            'user-list',
            'user-create',
            'user-edit',
            'user-delete',

            'menu',
            'client',
            'faq',
            'testimonial',
            'social-icon',
            'slider',
            'districts',
            'divisions',
            'categories',
            'sub-categories',
            'pages',
            'setting',

        ];



        foreach ($permissions as $permission) {

            Permission::create(['name' => $permission]);
        }
    }
}
