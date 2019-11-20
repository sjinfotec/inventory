<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\ShiftInformation;


class SttingShiftTimeController extends Controller
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
        return view('setting_shift_time');
    }

    /**
     * ユーザーシフト論理削除
     *
     * @return void
     */
    public function del(Request $request){
        $id = $request->id;
        $response = collect();
        $result = $this->dbConnectUpdate($id);
        if($result){
            $response->put('result',self::SUCCESS);
        }else{
            $response->put('result',self::FAILED);
        }
        return $response;
    }

    /**
     * シフト範囲削除
     *
     * @param Request $request
     * @return void
     */
    public function rangeDel(Request $request){
        $code = $request->user_code;
        $from = new Carbon($request->from);
        $from = $from->format("Y/m/d");
        $to = new Carbon($request->to);
        $to = $to->format("Y/m/d");
        $response = collect();
        $result = $this->dbConnectDel($code,$from,$to);
        if($result){
            $response->put('result',self::SUCCESS);
        }else{
            $response->put('result',self::FAILED);
        }
        return $response;
    }

    /**
     * シフト登録
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request){
        $response = collect();
        $code = $request->user_code;
        $time_table_no = $request->time_table_no;
        $department_code = $request->department_code;
            
        $from = new Carbon($request->from);
        $to = new Carbon($request->to);

        $result = $this->dbConnectInsert($code,$department_code,$time_table_no,$from,$to);
        if($result){
            $response->put('result',self::SUCCESS);
        }else{
            $response->put('result',self::FAILED);
        }
        return $response;
    }

    /**
     * DB書き込み(新規)
     *
     * @param [type] $code
     * @param [type] $shift_start_time
     * @param [type] $shift_end_time
     * @param [type] $from
     * @param [type] $to
     * @return void
     */
    private function dbConnectInsert($code,$department_code,$time_table_no,$from,$to){
        $systemdate = Carbon::now();
        DB::beginTransaction();
        try{
            $shift_info = new ShiftInformation();
            $shift_info->setUsercodeAttribute($code);
            $shift_info->setDepartmentcodeAttribute($department_code);
            $shift_info->setStarttargetdateAttribute($from);
            $shift_info->setEndtargetdateAttribute($to);
            $is_exists = $shift_info->isExistsShiftInfo();
            if($is_exists){
               $shift_info->delShiftInfo();
            }
            $shift_info->setWorkingtimetablenoAttribute($time_table_no);
            $shift_info->setCreatedatAttribute($systemdate);
            // from -> to までtarget_date登録する
            for ($i=$from; $i->lte($to); $i->addDay()) {
                $target_date = $i->format("Ymd"); 
                $shift_info->setTargetdateAttribute($target_date);
                $shift_info->insertUserShift();
            }
            DB::commit();
            return true;

        }catch(\PDOException $e){
            DB::rollBack();
            return false;
        }
    }

    /**
     * DB書き込み（UPDATE）
     *
     * @param [type] $id
     * @return void
     */
    private function dbConnectUpdate($id){
        DB::beginTransaction();
        try{
            DB::table('shift_informations')->where('id', $id)->update(['is_deleted' => 1]);
            DB::commit();
            return true;

        }catch(\PDOException $e){
            DB::rollBack();
            return false;
        }
    }

    /**
     * DB書き込み（UPDATE）
     *
     * @param [type] $id
     * @return void
     */
    private function dbConnectDel($code,$from,$to){
        DB::beginTransaction();
        try{
            $shift_info = new ShiftInformation();
            $shift_info->setUsercodeAttribute($code);
            $shift_info->setStarttargetdateAttribute($from);
            $shift_info->setEndtargetdateAttribute($to);
        
            $shift_info->delShiftInfo();
            DB::commit();
            return true;

        }catch(\PDOException $e){
            DB::rollBack();
            return false;
        }
    }
}
