<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\UserModel;
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
            'email' => 'required|email',
            'status' => 'required',
            'loginid' => 'required|unique:users,code|max:10',
            'password' => 'required|max:30',
        ],[
            'name.required'  => '社員名を入力してください',
            'name.max'  => '社員名の最大文字数は 30 です',
            'kana.required'  => 'ふりがなを入力してください',
            'email.required'  => 'メールアドレスを入力してください',
            'email.email'  => 'メールアドレスの入力形式で入力してください (例: sanjyo-tarou@ssjjoo.com)',
            'status.required' => '雇用形態を選択してください',
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
        $email = $request->email;
        $status = $request->status;
        $table_no = $request->table_no;
        $password = bcrypt($request->password);
        
        $result = $this->dbConnectInsert($code,$kana,$department_code,$name,$password,$email,$status,$table_no);
        if($result){
        }else{
            return false;
        }
    }

    /**
     * ユーザー削除
     *
     * @param Request $request
     * @return void
     */
    public function del(Request $request){
        $code = $request->user_code;
        $response = collect();
        $result = $this->dbConnectUpdate($code);
        if($result){
            $response->put('result',self::SUCCESS);
        }else{
            $response->put('result',self::FAILED);
        }
        return $response;
    }

    /**
     * 論理削除
     *
     * @param [type] $code
     * @return void
     */
    public function dbConnectUpdate($code){
        $users = new UserModel();
        $users->setCodeAttribute($code);
        
        DB::beginTransaction();
        try{
            $users->updateIsDelete();
            DB::commit();
            return true;

        }catch(\PDOException $e){
            DB::rollBack();
            return false;
        }
    }

    /**
     * DB書き込み（新規）
     *
     * @param [type] $id
     * @return void
     */
    private function dbConnectInsert($code,$kana,$department_code,$name,$password,$email,$status,$table_no){
        $users = new UserModel();
        $users->setCodeAttribute($code);
        $users->setDepartmentcodeAttribute($department_code);
        $users->setNameAttribute($name);
        $users->setKanaAttribute($kana);
        $users->setPasswordAttribute($password);
        $users->setEmailAttribute($email);
        $users->setEmploymentstatusAttribute($status);
        $users->setWorkingtimetablenoAttribute($table_no);
        
        DB::beginTransaction();
        try{
            $users->insertNewUser();
            DB::commit();
            return true;

        }catch(\PDOException $e){
            DB::rollBack();
            return false;
        }
    }

    /** ユーザー詳細取得
     *
     * @return list results
     */
    public function getUserDetails(Request $request){
        $code = $request->code;
        $users = new UserModel();
        $users->setCodeAttribute($code);
        $details = $users->getUserDetails();
        foreach ($details as $detail) {
            // $detail->password = dencrypt($detail->password);
        }
        
        return $details;
    }
}
