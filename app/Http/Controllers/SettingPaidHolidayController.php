<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\PaidHoliday;


class SettingPaidHolidayController extends Controller
{
    // メッセージ
    private $array_messagedata = array();

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 初期表示
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('setting_paid_holiday');
    }

    /**
     * 有給表示
     *
     * @param Request $request
     * @return void
     */
    public function get(Request $request){
        $year = $request->year;
        $department_code = $request->department_code;
        $user_code = $request->user_code;
        $paid_holiday = new PaidHoliday();
        $paid_holiday->setYearAttribute($year);
        $paid_holiday->setParamdepartmentcodeAttribute($department_code);
        $paid_holiday->setUserCodeAttribute($user_code);
        $results = $paid_holiday->getPaidData();
        return $results;
    }

    /**
     * 有給登録
     *
     * @param Request $request
     * @return void
     */
    public function updatePaidInformations(Request $request){
        $this->array_messagedata = array();
        $result = true;
        // リクエスト設定
        $usercode = Auth::user()->code;
        $details = $request->details;
        $target_year = $request->year;
        $systemdate = Carbon::now();
        // 有給登録
        DB::beginTransaction();
        try{
            $paid_holiday = new PaidHoliday();
            $paid_holiday->setYearAttribute($target_year);
            $paid_holiday->setCreateduserAttribute($usercode);
            $paid_holiday->setUpdateduserAttribute($usercode);
            $paid_holiday->setCreatedatAttribute($systemdate);
            $paid_holiday->setUpdatedatAttribute($systemdate);
            foreach($details as $detail){
                // detail["year"] が nullの場合は新規登録
                if(isset($detail["year"])){
                    // update
                    $paid_holiday->setUserCodeAttribute($detail["code"]);
                    $paid_holiday->setTypeAttribute($detail["type"]);
                    $paid_holiday->setPaidThisYearAttribute($detail["paid_this_year"]);
                    $paid_holiday->setPaidLastYearAttribute($detail["paid_last_year"]);
                    $paid_holiday->setPaidSumAttribute($detail["paid_sum"]);
                    $paid_holiday->updatePaidInformations();
                }else{
                    // insert
                    $paid_holiday->setUserCodeAttribute($detail["code"]);
                    $paid_holiday->setTypeAttribute($detail["type"]);
                    $paid_holiday->setPaidThisYearAttribute($detail["paid_this_year"]);
                    $paid_holiday->setPaidLastYearAttribute($detail["paid_last_year"]);
                    $paid_holiday->setPaidSumAttribute($detail["paid_sum"]);
                    $paid_holiday->insertPaidInformations();
                }
            }
            DB::commit();
            // $response->put(Config::get('const.PUT_ITEM.result'),Config::get('const.RESULT_CODE.success'));
            return response()->json(
                ['result' => $result,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            DB::rollBack();
           throw $pe;

        }catch(\Exception $e){
            DB::rollBack();
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }

}
