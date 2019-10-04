<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreUserPost;
use Illuminate\Support\Facades\Auth;
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
        Log::debug('$request->departmentCode = '.$request->departmentCode);
        Log::debug('$request->role = '.$request->role);
        $department_code = $request->departmentCode;
        $kana = $request->kana;
        $code = $request->code;
        $name = $request->name;
        $email = $request->email;
        $status = $request->status;
        $table_no = $request->table_no;
        $password = bcrypt($request->password);
        $role = $request->role;
        Log::debug('$role = '.$role);
        $from = "20000101";         // 有効期間 初期値

        if(isset($request->id)){    // UPDATE
            // $id = $request->id;
            // $result = $this->updateUser($id,$code,$kana,$department_code,$name,$password,$email,$status,$table_no);
        }else{                      // INSERT
            $result = $this->insertNewUser($code,$kana,$department_code,$name,$password,$email,$status,$table_no,$from,$role);
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
    private function insertNewUser($code,$kana,$department_code,$name,$password,$email,$status,$table_no,$from,$role){
        $users = new UserModel();
        $systemdate = Carbon::now();
        $user = Auth::user();
        $user_code = $user->code;
        $users->setApplytermfromAttribute($from);
        $users->setCodeAttribute($code);
        $users->setDepartmentcodeAttribute($department_code);
        $users->setNameAttribute($name);
        $users->setKanaAttribute($kana);
        $users->setPasswordAttribute($password);
        $users->setEmailAttribute($email);
        $users->setEmploymentstatusAttribute($status);
        $users->setWorkingtimetablenoAttribute($table_no);
        $users->setCreatedatAttribute($systemdate);
        $users->setCreateduserAttribute($user_code);
        $users->setRoleAttribute($role);
        Log::debug('$role = '.$role);
        
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
     * @param Request $request
     * @return response
     */
    public function fixUser(Request $request){
        $details = $request->details;
        $pass_word = $request->pass;
        $response = collect();
        $result = $this->fixData($details,$pass_word);
        if($result){
            $response->put('result',self::SUCCESS);
        }else{
            $response->put('result',self::FAILED);
        }
        return $response;
    }

    /**
     * UPDATE
     *
     * @param [type] $details
     * @return boolean
     */
    private function fixData($details,$pass_word){
        $systemdate = Carbon::now();
        $user_model = new UserModel();
        $user = Auth::user();
        $user_code = $user->code;

        DB::beginTransaction();
        try{
            foreach ($details as $detail) {
                $carbon = new Carbon($detail['apply_term_from']);
                $from = $carbon->copy()->format('Ymd');
                $user_model->setApplytermfromAttribute($from);
                $user_model->setCodeAttribute($detail['code']);
                $user_model->setNameAttribute($detail['name']);
                $user_model->setKanaAttribute($detail['kana']);
                $user_model->setDepartmentcodeAttribute($detail['department_code']);
                $user_model->setEmploymentstatusAttribute($detail['employment_status']);
                $user_model->setEmailAttribute($detail['email']);
                $user_model->setWorkingtimetablenoAttribute($detail['working_timetable_no']);
                $user_model->setPasswordAttribute($pass_word);
                $user_model->setRoleAttribute($role);
                
                // idもっているかどうか
                if(isset($detail['id'])){     // idもっている→UPDATE
                    $user_model->setIdAttribute($detail['id']);   
                    $user_model->setUpdatedatAttribute($systemdate);   
                    $user_model->setUpdateduserAttribute($user_code);   
                    $user_model->updateUser();
                }else{                      // idもっていない→INSERT
                    $user_model->setCreatedatAttribute($systemdate);
                    $user_model->setCreatedatAttribute($systemdate);
                    $user_model->setCreateduserAttribute($user_code);
                    $user_model->insertNewUser();
                }
            }   
            DB::commit();
            return true;

        }catch(\PDOException $e){
            Log::error($e->getMessage());
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
        $id = $request->id;
        $response = collect();
        $result = $this->updateIsDelete($id);
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
    public function updateIsDelete($id){
        $users = new UserModel();
        $users->setIdAttribute($id);
        
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
            if(isset($detail->apply_term_from)){
                // yyyy-mm-ddに変換
                $carbon = new Carbon($detail->apply_term_from);
                $detail->apply_term_from = $carbon->copy()->format("Y-m-d");

            }
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
        $systemdate = Carbon::now();
        $user = Auth::user();
        $user_code = $user->code;

        // パスワード変更
        DB::beginTransaction();
        try{
            $users = new UserModel();
            $users->setCodeAttribute($code);
            $users->setPasswordAttribute($pass_word);
            $users->setUpdateduserAttribute($user_code);
            $users->setUpdatedatAttribute($systemdate);
            $users->updatePassWord();
            DB::commit();
            $response->put('result',self::SUCCESS);

        }catch(\PDOException $e){
            DB::rollBack();
            $response->put('result',self::FAILED);
        }
        return $response;

    }

    public function releaseCardInfo(Request $request){
        $card_idm = $request->card_idm;
        $response = collect();
        $systemdate = Carbon::now();
        $user = Auth::user();
        $user_code = $user->code;
    
        // パスワード変更
        DB::beginTransaction();
        try{
            DB::table('card_informations')->where('card_idm', $card_idm)->update(['is_deleted' => 1,'updated_user' => $user_code ,'updated_at' => $systemdate]);
            DB::commit();
            $response->put('result',self::SUCCESS);

        }catch(\PDOException $e){
            DB::rollBack();
            $response->put('result',self::FAILED);
        }
        return $response;
    }
}
