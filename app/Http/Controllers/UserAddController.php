<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Http\Requests\StoreUserPost;
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

    /**
     * 登録
     *
     * @param Request $request
     * @return void
     */
    public function store(StoreUserPost $request){
        $department_code = $request->DepartmentCode;
        $kana = $request->kana;
        $code = $request->code;
        $name = $request->name;
        $email = $request->email;
        $status = $request->status;
        $table_no = $request->table_no;
        $password = bcrypt($request->password);

        if(isset($request->id)){    // UPDATE
            $id = $request->id;
            $result = $this->updateUser($id,$code,$kana,$department_code,$name,$password,$email,$status,$table_no);
        }else{                      // INSERT
            $result = $this->insertNewUser($code,$kana,$department_code,$name,$password,$email,$status,$table_no);
        }
        if($result){
        }else{
            return false;
        }
    }

    /**
     * ユーザー追加
     *
     * @param [type] $code
     * @param [type] $kana
     * @param [type] $department_code
     * @param [type] $name
     * @param [type] $password
     * @param [type] $email
     * @param [type] $status
     * @param [type] $table_no
     * @return void
     */
    private function insertNewUser($code,$kana,$department_code,$name,$password,$email,$status,$table_no){
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

    /**
     * ユーザー編集
     *
     * @param [type] $code
     * @param [type] $kana
     * @param [type] $department_code
     * @param [type] $name
     * @param [type] $password
     * @param [type] $email
     * @param [type] $status
     * @param [type] $table_no
     * @return void
     */
    public function updateUser($id,$code,$kana,$department_code,$name,$password,$email,$status,$table_no){
        $users = new UserModel();
        DB::beginTransaction();
        try{
            $users->setIdAttribute($id);
            $users->setDepartmentcodeAttribute($department_code);
            $users->setCodeAttribute($code);
            $users->setNameAttribute($name);
            $users->setKanaAttribute($kana);
            $users->setPasswordAttribute($password);
            $users->setEmailAttribute($email);
            $users->setEmploymentstatusAttribute($status);
            $users->setWorkingtimetablenoAttribute($table_no);

            $users->updateUser();
            DB::commit();
            return true;

        }catch(\PDOException $e){
            DB::rollBack();
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
        $result = $this->updateIsDelete($code);
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
    public function updateIsDelete($code){
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

    /** 詳細取得
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

    /**
     * パスワード変更
     *
     * @return void
     */
    public function passChange(Request $request){
        $code = $request->user_code;
        $pass_word = bcrypt($request->password);
        $response = collect();

        // パスワード変更
        DB::beginTransaction();
        try{
            $users = new UserModel();
            $users->setCodeAttribute($code);
            $users->setPasswordAttribute($pass_word);
            $users->updatePassWord();
            DB::commit();
            $response->put('result',self::SUCCESS);

        }catch(\PDOException $e){
            DB::rollBack();
            $response->put('result',self::FAILED);
        }
        return $response;

    }
}
