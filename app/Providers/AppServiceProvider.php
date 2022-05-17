<?php

namespace App\Providers;

use App\Models\PmsModels\Menu\Menu;
use Illuminate\Support\ServiceProvider;
use View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer( // for admin menu --------------
            [
                'pms.backend.menus.left-menu',
            ],
            function ($view)
            {
                $menus=Menu::with('subMenu')->where(['menu_for'=>Menu::ADMIN_MENU,'status'=>Menu::ACTIVE])->orderBy('serial_num','ASC')->get();

                $view->with(['menus'=>$menus]);
            });
    }
}
