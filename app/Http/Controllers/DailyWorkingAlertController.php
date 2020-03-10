<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\WorkTime;
use App\Http\Controllers\ApiCommonController;

class DailyWorkingAlertController extends Controller
{

    private $array_messagedata = array();

    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        return view('daily_working_alert');
    }

    /**
     * 日次アラート表示 
     *
     * @return void
     */
    public function show(Request $request){
        $this->array_messagedata = array();
        $details = new Collection();
        $result_working = new Collection();
        $date_name = '';
        $result = true;
        $killvalue = false; // 未使用
        try{
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $result_working, 'datename' => $date_name,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            if (!isset($params['alert_form_date'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "alert_form_date", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $result_working, 'datename' => $date_name,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $alert_form_date = $params['alert_form_date'];      // YYYYMMDD
            // datetoをdatefromにし、datefromは１週間前に設定する
            $alert_to_date = $alert_form_date;
            $dt = new Carbon($alert_form_date.' 00:00:00');
            $dtfrom = date("Y-m-d H:i:s",strtotime($dt."-1 week"));
            $alert_form_date = date_format(new Carbon($dtfrom), 'Ymd');

            $employmentstatus = null;
            if(isset($params['employmentstatus'])){
                $employmentstatus = $params['employmentstatus'];
            }
            $departmentcode = null;
            if(isset($params['departmentcode'])){
                $departmentcode = $params['departmentcode'];
            }
            $usercode = null;
            if(isset($params['usercode'])){
                $usercode = $params['usercode'];
            }
            $this->collect_massegedata = collect();
    
            $details = new Collection();
            $work_time_model = new WorkTime();
            // 打刻時刻を取得
            $work_time_model->setParamdatefromAttribute($alert_form_date);
            $work_time_model->setParamdatetoAttribute($alert_to_date);
            $work_time_model->setParamemploymentstatusAttribute($employmentstatus);
            $work_time_model->setParamDepartmentcodeAttribute($departmentcode);
            $work_time_model->setParamUsercodeAttribute($usercode);
            $work_time_model->setParamStartDateAttribute($alert_form_date);
            $work_time_model->setParamEndDateAttribute($alert_to_date);
            $chk_work_time = $work_time_model->chkWorkingTimeData();
            if ($chk_work_time) {
                $details = $work_time_model->getdailyAlertData();
                if (count($details) == 0) {
                    $this->array_messagedata[] = Config::get('const.MSG_INFO.no_alert_data');
                    return response()->json(
                        ['result' => false, 'details' => $result_working, 'datename' => $date_name,
                        Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                    );
                }
            } else {
                $this->array_messagedata[] = $work_time->getMassegedataAttribute();
                return response()->json(
                    ['result' => false, 'details' => $result_working, 'datename' => $date_name,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            // 日付編集
            // 開始日付のフォーマット 2019年10月01日(火)
            $apicommon = new ApiCommonController();
            $date_name = $apicommon->getYMDWeek($alert_form_date);
            
            $result_working = $details;
            // $result_working = $details->where('business_kubun', Config::get('const.C007.basic'));
            if (count($result_working) == 0) {
                $this->array_messagedata[] = Config::get('const.MSG_INFO.no_alert_data');
                return response()->json(
                    ['result' => false, 'details' => $result_working, 'datename' => $date_name,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
    

            return response()->json(
                ['result' => true, 'details' => $result_working, 'datename' => $date_name,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            throw $e;
        }

    }

}
