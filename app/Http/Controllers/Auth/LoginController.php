<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'code';
    }

    /*
     *  ログイン項目追加による
     *  2020/08/11
    */
    protected function credentials(Request $request)
    {
        $temporary = $request->only($this->username(), 'password');
        $temporary['account_id'] = $request->account_id;
        $temporary['is_deleted'] = 0;
        Log::debug('credentials $temporary[code]'.$temporary['code']);
        Log::debug('credentials $temporary[account_id]'.$temporary['account_id']);
        Log::debug('credentials $temporary[is_deleted]'.$temporary['is_deleted']);
    
        return $temporary;
    }
}
