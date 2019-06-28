<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\ShiftTime;


class CreateShiftTimeController extends Controller
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
        return view('create_shift_time');
    }

    public function store(Request $request){
        $shift_start_time = $request->start;
        $shift_end_time = $request->end;
        
        $response = collect();              // 端末の戻り値
        // 存在チェック
        $is_shift_exists = DB::table('shift_times')->where('shift_start_time', $shift_start_time)->where('shift_end_time', $shift_end_time)->exists();
        if($is_shift_exists){
            // 既に存在するので何もしない
            $response->put('result',self::FAILED);
        }else{
            // 新規登録
            $result = $this->dbConnectInsert($shift_start_time,$shift_end_time);
            if($result){
                $response->put('result',self::SUCCESS);
            }else{
                $response->put('result',self::FAILED);
            }
        }
    
        return $response;
    }

    /**
     * DB書き込み(新規)
     *
     * @param [type] $shift_start_time
     * @param [type] $shift_end_time
     * @return void
     */
    private function dbConnectInsert($shift_start_time,$shift_end_time){
        $systemdate = Carbon::now();
        $shift_time = new ShiftTime();
        DB::beginTransaction();
        try{
            $shift_time->setStartTimeAttribute($shift_start_time);
            $shift_time->setEndTimeAttribute($shift_end_time);
            $shift_time->setSystemDateAttribute($systemdate);
            $shift_time->insertShiftTime();

            DB::commit();
            return true;

        }catch(\PDOException $e){
            DB::rollBack();
            return false;
        }
    }

    /**
     * 登録済シフト時間表示
     *
     * @return void
     */
    public function get(){
        $shift_times = DB::table('shift_times')->where('is_deleted', 0)->get();
        return $shift_times;
    }
}
