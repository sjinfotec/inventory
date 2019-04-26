<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class ApiGetAttendanceResultController extends Controller
{
    /**
     * 初期処理
     *
     * @param Request $request
     * @return void
     */
    public function index(){}

    /**
     * 登録
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request) { 
        $card_id = $request->card_id;
        $mode = $request->mode;
        $user = new User();
        // カード情報存在チェック
        // $is_exists = DB::table($table_name)->where($column, $user_id)->exists();
        $user_data = $user->getUserData();

        return $user_data;
    }

    /**
     * 1件取得
     *
     * @param [type] $id
     * @return void
     */
    public function show($id) { }

    /**
     * 更新
     *
     * @param Request $request
     * @param [type] $id
     * @return void
     */
    public function update(Request $request, $id) { }

    /**
     * 削除
     *
     * @param [type] $id
     * @return void
     */
    public function destroy($id) { }

}
