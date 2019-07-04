<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class UserAddController extends Controller
{
    const SUCCESS = 0;
    const FAILED = 1;

    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        return view('user_add');
    }

    public function store(Request $request){
        $validatedData = $request->validate([
            'name' => 'required|string|max:30',
            'kana' => 'required',
            'loginid' => 'required|unique:users,code|max:10|alpha_num',
            'password' => 'required|max:30|alpha_num',
        ],[
            'name.required'  => '社員名を入力してください',
            'name.max'  => '社員名の最大文字数は 30 です',
            'kana.required'  => 'ふりがなを入力してください',
            'loginid.required'  => 'ログインIDを入力してください',
            'loginid.unique'  => 'ログインIDは既に使用済です',
            'loginid.max'  => 'ログインIDの最大文字数は 10 です',
            'password.required'  => 'パスワードを入力してください',
            'password.max'  => 'パスワードの最大文字数は 30 です',
        ]);
        
        $department_code = $request->departmentCode;
        $kana = $request->kana;
        $code = $request->loginid;
        $name = $request->name;
        $password = bcrypt($request->password);
    }
}
