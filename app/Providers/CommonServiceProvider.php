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
        // Log::debug('boot ip_address = '.$request-> ip());

    }
}
