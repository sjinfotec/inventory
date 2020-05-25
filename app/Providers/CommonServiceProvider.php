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

        //account
        $account_data = collect([
            'account_id' => Config::get('const.ACCOUNTID.account_id')
        ]);
        View::share('account_datas', $account_data);

        //menu selection
        $menu_model = new MenuItemSelection();
        $menu_model->setParamaccountidAttribute(Config::get('const.ACCOUNTID.account_id'));
        $menu_model->setParamselectioncodeAttribute(Config::get('const.EDITION.EDITION'));
        $menu_data = $menu_model->getMenuItem();
        View::share('menu_selections', $menu_data);

        //const
        $general_model = new GeneralCodes();
        $general_datas = $general_model->getGeneralcode();
        View::share('const_general_datas', $general_datas);
    }
}
