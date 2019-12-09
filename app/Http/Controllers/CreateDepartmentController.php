<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\StoreDepartmentPost;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Department;

class CreateDepartmentController extends Controller
{
    const SUCCESS = 0;
    const FAILED = 1;
    // メッセージ
    private $array_messagedata = array();

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
        $details = new Collection();
        try {
            $code = $request->code;
            $department_model = new Department();
            $dt = new Carbon();
            $from = $dt->copy()->format('Ymd');
            $department_model->setParamapplytermfromAttribute($from);
            $department_model->setParamcodeAttribute($code);
            $details = $department_model->getDetails();
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            throw $e;
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
        $terms = new Collection();
        try {
            $code = $request->code;
            $terms = DB::table('departments')->where('code', $code)->where('is_deleted', 0)->orderby('apply_term_from','asc')->get();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
        return $terms;
    }

    /**
     * 部署登録
     *
     * @param Request $request
     * @return void
     */
    public function store(StoreDepartmentPost $request){
        $code = '';
        try {
            $kbn = $request->kbn;
            $code = $request->code;
            $name = $request->name;
            if ($kbn == Config::get('const.DB_KBN.store')) {
                if (isset($code)) {
                    $this->array_messagedata[] = Config::get('const.MSG_ERROR.already_data');
                    return response()->json(
                        ['result' => false, 'code' => $code,
                        Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                    );
                }
            }
            $code = $this->insert($kbn, $code, $name);
    
            return response()->json(
                ['result' => true, 'code' => $code,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            throw $e;
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
    private function insert($kbn, $code, $name){
        DB::beginTransaction();
        try{
            $systemdate = Carbon::now();
            $user = Auth::user();
            $department = new Department();
            $user_code = $user->code;
            if ($kbn == Config::get('const.DB_KBN.store')) {
                $from = Config::get('const.INIT_DATE.initdate');
                $max_code = $department->getMaxCode();          // code 自動採番
                if (isset($max_code)) {
                    $code = $max_code + 1;
                } else {
                    $code = 1;
                }
            } else {
                $maxno = $code;
                if(isset($data[0]['apply_term_from'])){
                    $carbon = new Carbon($data[0]['apply_term_from']);
                    Log::debug("carbon = ".$carbon);
                    $from = $carbon->copy()->format('Ymd');
                    $term_from = $from;
                    Log::debug("term_from = ".$term_from);
                }
            }

            $department->setApplytermfromAttribute($from);
            $department->setCodeAttribute($code);
            $department->setNameAttribute($name);
            $department->setCreatedatAttribute($systemdate);
            $department->setCreateduserAttribute($user_code);
            $department->insertDepartment();
        
            DB::commit();
            return $code;

        }catch(\PDOException $pe){
            DB::rollBack();
            throw $pe;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
            throw $e;
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
