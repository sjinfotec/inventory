<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use App\GeneralCodes;
use App\MenuItemSelection;

class CommonServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        $menu_model = new MenuItemSelection();
        $menu_model->setParamaccountidAttribute(
            array(Config::get('const.ACCOUNTID.account_id')));
        $menu_model->setParamselectioncodeAttribute(
            array(Config::get('const.EDITION.EDITION')));
        $menu_data = $menu_model->getMenuItem();

        Log::debug('$menu_data = '.count($menu_data));
        View::share('menu_selections', $menu_data);

        //const
        $general_model = new GeneralCodes();
        $general_model->setParamarrayidentificationidAttribute(
            array(Config::get('const.C037.value')));
        $general_data_C037 = $general_model->getGeneralcode();
        Log::debug('$general_data_C037 = '.count($general_data_C037));
        View::share('const_general_data_C037', $general_data_C037);
    }
}
