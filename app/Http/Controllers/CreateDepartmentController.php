<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Http\Requests\StoreDepartmentPost;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Department;

class CreateDepartmentController extends Controller
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
        return view('create_department');
    }

    /**
     * 部署名取得
     *
     * @param Request $request
     * @return void
     */
    public function getDetails(Request $request){
        $code = $request->code;
        $details = DB::table('departments')->where('code', $code)->where('is_deleted', 0)->orderby('id','asc')->get();
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
     * 有効期間取得
     *
     * @param Request $request
     * @return void
     */
    public function getDepartmentApplyTerm(Request $request){
        $code = $request->code;
        $terms = DB::table('departments')->where('code', $code)->where('is_deleted', 0)->orderby('apply_term_from','asc')->get();
        return $terms;
    }

    /**
     * 部署登録
     *
     * @param Request $request
     * @return void
     */
    public function store(StoreDepartmentPost $request){
        $name = $request->name;
        if(isset($request->id)){    // UPDATE
            $id = $request->id;
            $result = $this->updateName($id,$name);
        }else{                      // INSERT
             $result = $this->insert($name);
        }
       
        if($result){
        }else{
            return false;
        }
    }

    /**
     * 修正ボタン押下 
     *
     * @param Request $request
     * @return response
     */
    public function fix(Request $request){
        $details = $request->details;
        $response = collect();
        $result = $this->fixData($details);
        if($result){
            $response->put('result',self::SUCCESS);
        }else{
            $response->put('result',self::FAILED);
        }
        return $response;
    }

    /**
     * INSERT
     *
     * @param [type] $name
     * @return void
     */
    private function insert($name){
        $systemdate = Carbon::now();
        $user = Auth::user();
        $department = new Department();
        $user_code = $user->code;
        $from = "20000101";
        DB::beginTransaction();
        try{
            $max_code = $department->getMaxCode();          // code 自動採番
            $code = $max_code + 1;
            $department->setApplytermfromAttribute($from);
            $department->setCodeAttribute($code);
            $department->setNameAttribute($name);
            $department->setCreatedatAttribute($systemdate);
            $department->setCreateduserAttribute($user_code);
            $department->insertDepartment();
        
            DB::commit();
            return true;

        }catch(\PDOException $e){
            Log::error($e->getMessage());
            DB::rollBack();
            return false;
        }
    }

    /**
     * 修正
     *
     * @param [type] $details
     * @return boolean
     */
    private function fixData($details){
        $systemdate = Carbon::now();
        $department = new Department();
        $user = Auth::user();
        $user_code = $user->code;

        DB::beginTransaction();
        try{
            foreach ($details as $detail) {
                $carbon = new Carbon($detail['apply_term_from']);
                $from = $carbon->copy()->format('Ymd');
                $department->setApplytermfromAttribute($from);
                $department->setCodeAttribute($detail['code']);
                $department->setNameAttribute($detail['name']);
                // idもっているかどうか
                if(isset($detail['id'])){     // idもっている→UPDATE
                    $department->setIdAttribute($detail['id']);   
                    $department->setUpdatedatAttribute($systemdate);   
                    $department->setUpdateduserAttribute($user_code);   
                    $department->updateDepartment();
                }else{                      // idもっていない→INSERT
                    $department->setCreatedatAttribute($systemdate);
                    $department->setCreateduserAttribute($user_code);
                    $department->insertDepartment();
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
     * 論理削除
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
     * 上書き(Is_deleted)
     *
     * @param [type] $id
     * @return void
     */
    public function updateIsDelete($id){
        DB::beginTransaction();
        try{
            DB::table('departments')
            ->where('id', $id)
            ->update(['is_deleted' => 1]);
            DB::commit();
            return true;

        }catch(\PDOException $e){
            DB::rollBack();
            return false;
        }
    }

    /**
     * 上書き(name)
     *
     * @param [type] $id
     * @param [type] $name
     * @return void
     */
    public function updateName($id,$name){
        $systemdate = Carbon::now();
        DB::beginTransaction();
        try{
            DB::table('departments')
            ->where('id', $id)
            ->where('is_deleted', 0)
            ->update(['name' => $name,'updated_at' => $systemdate]);
            DB::commit();
            return true;

        }catch(\PDOException $e){
            DB::rollBack();
            return false;
        }
    }
}
