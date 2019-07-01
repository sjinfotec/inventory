<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ApiCommonController extends Controller
{
    /**
     * ユーザーリスト取得
     *
     * @return void
     */
    public function getUserList(){
        $users = DB::table('users')->where('is_deleted', 0)->orderby('id','asc')->get();
        return $users;
    }
}
