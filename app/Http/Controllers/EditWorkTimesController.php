<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Http\Requests\StoreUserPost;
use Illuminate\Support\Facades\Auth;
use App\WorkTime;
use App\UserHolidayKubun;
use Carbon\Carbon;


class EditWorkTimesController extends Controller
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
        return view('edit_work_times');
    }

    /** 詳細取得
     *
     * @return list results
     */
    public function get(Request $request){
        $code = $request->code;
        $year = $request->year;
        $month = $request->month;
        $closing = DB::table('settings')->where('fiscal_year', $year)->where('fiscal_month', $month)->where('is_deleted', 0)->value('closing');
        $ymd_start = $year."/".$month."/".$closing." 00:00:00";
        $ymd_end = $year."/".$month."/".$closing." 23:59:59";
        $closing_start = new Carbon($ymd_start);
        $closing_end = new Carbon($ymd_end);
        $closing_start = $closing_start->copy()->subMonth()->addDay()->format('Y/m/d H:i:s');
        $closing_end = $closing_end->format('Y/m/d H:i:s');

        $work_times = new WorkTime();
        $work_times->setUsercodeAttribute($code);
        $work_times->setParamStartDateAttribute($closing_start);
        $work_times->setParamEndDateAttribute($closing_end);

        $details = $work_times->getUserDetails();
        $count = 0;
        $before_date = "";
        foreach ($details as $detail) {
            $detail->kbn_flag = 0;
            $detail->user_holiday_kbn="";
            if(isset($detail->record_time)){
                $carbon = new Carbon($detail->record_time);
                $detail->date = $carbon->copy()->format('Y/m/d');
                $search_date = $carbon->copy()->format('Ymd');
                // 個人休暇区分取得
                $holiday_kbn = DB::table('user_holiday_kubuns')->where('working_date', $search_date)->where('user_code', $code)->where('is_deleted', 0)->value('holiday_kubun');
                if(isset($holiday_kbn)){
                    $detail->user_holiday_kbn = $holiday_kbn;
                }
                $detail->time = $carbon->copy()->format('H:i');
                if($detail->date != $before_date){
                    $detail->kbn_flag = 1;
                }
                $before_date = $detail->date;
            }
        }
        
        return $details;
    }

    /**
     * 勤怠新規登録
     *
     * @param Request $request
     * @return response
     */
    public function add(Request $request){
        // request
        $date = $request->date;
        $time = $request->time;
        $carbon = new Carbon($date);
        $date = $carbon->format("Y/m/d");
        $record_time = $date." ".$time;     // DB用
        $code = $request->user_code;
        $mode = $request->mode;
        $holiday_kbn = $request->holiday_kbn;
        $work_time = new WorkTime();
        $systemdate = Carbon::now();
        $department_code = DB::table('users')->where('code', $code)->where('is_deleted', 0)->value('department_code');
        $user = Auth::user();
        $user_code = $user->code;
        $response = collect();

        if($holiday_kbn != ""){     // 休暇区分のみ登録
            DB::beginTransaction();
                try{
                    $record_time = $date;
                    $ymd = new Carbon($record_time);
                    $working_date = $ymd->copy()->format('Ymd');
                    $user_holiday = new UserHolidayKubun();
                    $user_holiday->setUsercodeAttribute($code);
                    $user_holiday->setWorkingdateAttribute($working_date);
                    $user_holiday->setSystemDateAttribute($systemdate);
                    // 既に存在する場合は論理削除する
                    $is_exists = $user_holiday->isExistsKbn();
                    if($is_exists){
                        $user_holiday->delKbn();
                    }
                    $user_holiday->setDepartmentcodeAttribute($department_code);
                    $user_holiday->setHolidaykubunAttribute($holiday_kbn);
                    $user_holiday->setCreateduserAttribute($user_code);
                    $user_holiday->insertKbn();

                    // 空の出勤データも登録
                    $work_time->setUsercodeAttribute($code);
                    $work_time->setDepartmentcodeAttribute($department_code);
                    $record_time = $date." "."0:00:00";
                    $work_time->setRecordtimeAttribute($record_time);
                    $work_time->setCreateduserAttribute($user_code);
                    $work_time->setSystemDateAttribute($systemdate);
                    $result = $work_time->insertWorkTime();
            
                    DB::commit();
                    $result = true;

                }catch(\PDOException $e){
                    DB::rollBack();
                    $result = false;
                }
    
        }else{                      // 通常の登録
            DB::beginTransaction();
                try{
                    $work_time->setUsercodeAttribute($code);
                    $work_time->setDepartmentcodeAttribute($department_code);
                    $work_time->setRecordtimeAttribute($record_time);
                    $work_time->setModeAttribute($mode);
                    $work_time->setCreateduserAttribute($user_code);
                    $work_time->setSystemDateAttribute($systemdate);
                    $result = $work_time->insertWorkTime();
                    DB::commit();
                    $result = true;

                }catch(\PDOException $e){
                    DB::rollBack();
                    $result = false;
                }
        }
        if($result){
            $response->put('result',self::SUCCESS);
        }else{
            $response->put('result',self::FAILED);
        }
        return $response;
        
    }

    /**
     * 編集登録
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request){
       // request
        $details = $request->details;
        $converts = array();
        $response = collect();
        // foreach ($details as $index => $detail) {
        // }

        $result = $this->insertWorkTime($details);
        if($result){
            $response->put('result',self::SUCCESS);
        }else{
            $response->put('result',self::FAILED);
        }
        return $response;
    }

    /**
     * 登録
     *
     * @param [type] $details
     * @return void
     */
    private function insertWorkTime($details){
        $work_time = new WorkTime();
        $user_holiday = new UserHolidayKubun();
        $systemdate = Carbon::now();
        $user = Auth::user();
        $converts = array();
        $converts = $details;       // 個人休暇登録用
        $user_code = $user->code;
        DB::beginTransaction();
            $work_time->setCreateduserAttribute($user_code);
            $work_time->setSystemDateAttribute($systemdate);
        try{
            foreach ($details as $detail) {
                // 論理削除新規
                $work_time->setIdAttribute($detail['id']);
                $work_time->delWorkTime();
                // 新規登録
                if(!isset($detail['time'])){
                    $detail['time'] = "00:00:00";
                }
                $record_time = $detail['date']." ".$detail['time'];
                $work_time->setUsercodeAttribute($detail['user_code']);
                $work_time->setDepartmentcodeAttribute($detail['department_code']);
                $work_time->setModeAttribute($detail['mode']);
                $work_time->setRecordtimeAttribute($record_time);

                $work_time->insertWorkTime();
                if($detail['kbn_flag'] == 1){         // 個人休暇が入っているものはuser_holiday_kubunsへ登録
                    $date = new Carbon($detail['record_time']);
                    $working_date = $date->copy()->format('Ymd');
                    $user_holiday->setUsercodeAttribute($detail['user_code']);
                    $user_holiday->setWorkingdateAttribute($working_date);
                    $user_holiday->setSystemDateAttribute($systemdate);
                    // 既に存在する場合は論理削除する
                    $is_exists = $user_holiday->isExistsKbn();
                    if($is_exists){
                        $user_holiday->delKbn();
                    }
                    $user_holiday->setDepartmentcodeAttribute($detail['department_code']);
                    $user_holiday->setHolidaykubunAttribute($detail['user_holiday_kbn']);
                    $user_holiday->setCreateduserAttribute($user_code);
                    $user_holiday->insertKbn();
                }
            }
            
            DB::commit();
            return true;

        }catch(\PDOException $e){
            DB::rollBack();
            return false;
        }
    }

    /**
     * レコード削除
     *
     * @param Request $request
     * @return void
     */
    public function del(Request $request){
        $id = $request->id;
        $work_time = new WorkTime();
        $systemdate = Carbon::now();
        $response = collect();
        
        DB::beginTransaction();
        try{
            $work_time->setIdAttribute($id);
            $work_time->setSystemDateAttribute($systemdate);
            $result = $work_time->delWorkTime();
            DB::commit();
            $result = true;

        }catch(\PDOException $e){
            DB::rollBack();
            $result = false;
        }
        if($result){
            $response->put('result',self::SUCCESS);
        }else{
            $response->put('result',self::FAILED);
        }
        return $response;
    }
}
