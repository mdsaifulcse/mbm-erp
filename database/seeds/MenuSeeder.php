<?php

use Illuminate\Database\Seeder;
use App\Models\PmsModels\Menu\Menu;
use App\Models\PmsModels\Menu\SubMenu;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        $menus = [
            [
                'id'=>'1',
                'name'=>'Acl',
                'url'=>'#:;',
                'icon_class'=>'fa fa-home',
                'serial_num'=>'1',
                'slug'=>'["menu"]',
            ],

            [
                'id'=>'2',
                'name'=>'Setting',
                'url'=>'#:;',
                'icon_class'=>'fa fa-home',
                'serial_num'=>'2',
                'slug'=>'["menu"]',
            ],
        ];


        Menu::insert($menus);

        // -------------------------
        $subMenus = [

            [
                'id'=>'1',
                'menu_id'=>'1',
                'name'=>'Role',
                'url'=>'pms/acl/roles',
                'icon_class'=>'fa fa-home',
                'serial_num'=>'1',
                'slug'=>'["role-delete","role-edit","role-create","role-list"]',
            ],

            [
                'id'=>'2',
                'menu_id'=>'2',
                'name'=>'Menu',
                'url'=>'pms/admin/menu',
                'icon_class'=>'fa fa-home',
                'serial_num'=>'2',
                'slug'=>'["menu"]',
            ],

            [
                'id'=>'12',
                'menu_id'=>'2',
                'name'=>'Users',
                'url'=>'pms/admin/users',
                'icon_class'=>'fa fa-user',
                'serial_num'=>'12',
                'slug'=>'["setting"]',
            ],
        ];

        SubMenu::insert($subMenus);

    }
}
