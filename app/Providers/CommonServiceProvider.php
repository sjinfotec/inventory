<?php

namespace App\Providers;
use Illuminate\Http\Request;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\GeneralCodes;
use App\MenuItemSelection;
use App\FeatureItemSelection;


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
    public function boot(Request $request)
    {
        // $user = Auth::user();
        // $login_user_code = $user->code;
        // $login_user_code_4 = substr($login_user_code, 0 ,4);
        // Log::debug('boot login_user_code = '.$login_user_code);

        // //account
        // $account_data = collect([
        //     'account_id' => Config::get('const.ACCOUNTID.account_id'),
        //     'edition' => Config::get('const.EDITION.EDITION')
        // ]);
        // View::share('account_datas', $account_data);
        Log::debug('boot ip_address = '.$request-> ip());

        //menu selection
        $menu_model = new MenuItemSelection();
        $menu_model->setParamaccountidAttribute(Config::get('const.TRIALACCOUNTID.account_id'));
        $menu_model->setParamselectioncodeAttribute(Config::get('const.EDITION.EDITION'));
        $menu_data = $menu_model->getMenuItem();
        View::share('menu_selections', $menu_data);

        //feature selection
        $feature_model = new FeatureItemSelection();
        $feature_model->setParamaccountidAttribute(Config::get('const.TRIALACCOUNTID.account_id'));
        $feature_model->setParamselectioncodeAttribute(Config::get('const.EDITION.EDITION'));
        $feature_data = $feature_model->getItem();
        View::share('feature_item_selections', $feature_data);
        $collect_feature_data = collect($feature_data);
        //const
        $general_model = new GeneralCodes();
        $general_datas = $general_model->getGeneralcode();
        View::share('const_general_datas', $general_datas);
    }
}
