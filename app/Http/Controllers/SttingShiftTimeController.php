<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\ShiftInformation;


class SttingShiftTimeController extends Controller
{

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
     * シフト登録
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request){
        $this->array_messagedata = array();
        $result = true;
        try {
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            if (!isset($params['form'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "form", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $data = $params['form'];
            $result = $this->insert($data);
            return response()->json(
                ['result' => $result,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
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
    private function insert($data){
        $systemdate = Carbon::now();
        DB::beginTransaction();
        try{
            $working_timetable_no = $data["working_timetable_no"];
            $user_code = $data["user_code"];
            $department_code = $data["department_code"];
            $shift_start_time = $data["shift_start_time"];
            $shift_end_time = $data["shift_end_time"];
            $from = new Carbon($shift_start_time);
            $to = new Carbon($shift_end_time);
            $shift_info_model = new ShiftInformation();
            $shift_info_model->setUsercodeAttribute($data["user_code"]);
            $shift_info_model->setDepartmentcodeAttribute($data["department_code"]);
            $shift_info_model->setStarttargetdateAttribute($from);
            $shift_info_model->setEndtargetdateAttribute($to);
            $is_exists = $shift_info_model->isExistsShiftInfo();
            if($is_exists){
               $shift_info_model->delShiftInfo();
            }
            $shift_info_model->setWorkingtimetablenoAttribute($data["working_timetable_no"]);
            $shift_info_model->setCreatedatAttribute($systemdate);
            // from -> to までtarget_date登録する
            for ($i=$from; $i->lte($to); $i->addDay()) {
                $target_date = $i->format("Ymd"); 
                $shift_info_model->setTargetdateAttribute($target_date);
                $shift_info_model->insertUserShift();
            }
            DB::commit();
            return true;

        }catch(\PDOException $e){
            DB::rollBack();
            return false;
        }
    }
}
