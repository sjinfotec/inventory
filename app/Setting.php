<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;


/**
 * テーブル：設定マスタ（settings）のモデル
 *      アクセサ定義
 * @author      o.shindo
 * @version     1.00    20190629
*/
class Setting extends Model
{
    protected $table = 'settings';
    protected $guarded = array('id');

    //--------------- 項目属性 -----------------------------------
    private $account_id;                    // アカウントID
    private $fiscal_year;                   // 年度
    private $fiscal_month;                  // 月
    private $closing;                       // 締日
    private $uplimit_time;                  // 上限残業時間
    private $statutory_uplimit_time;        // 法定上限残業時間
    private $time_unit;                     // 時間単位
    private $time_rounding;                 // 時間の丸め
    private $calc_auto_time;                // 集計自動起動時刻
    private $max_1month_total;              // １ヶ月累計
    private $max_2month_total;              // ２ヶ月累計
    private $max_3month_total;              // ３ヶ月累計
    private $max_6month_total;              // ６ヶ月累計
    private $max_12month_total;             // １年間累計
    private $ave_2_6_time_sp;               // ２－６ヶ月平均（特別条項）
    private $max_12month_total_sp;          // １２ヶ月累計（特別条項）
    private $max_1month_total_sp;           // １ヶ月累計（特別条項）
    private $count_sp;                      // 特別条項の回数
    private $beginning_month;               // 期首月
    private $interval;                      // 勤務間インターバル
    private $year;                          // 年
    private $created_user;                  // 作成ユーザー
    private $updated_user;                  // 修正ユーザー
    private $created_at;                    // 作成日次
    private $updated_at;                    // 更新日時
    private $is_deleted;                    // 削除フラグ

    // アカウントID
    public function getAccountidAttribute()
    {
        return $this->account_id;
    }

    public function setAccountidAttribute($value)
    {
        $this->account_id = $value;
    }

    // 年度
    public function getFiscalyearAttribute()
    {
        return $this->fiscal_year;
    }

    public function setFiscalyearAttribute($value)
    {
        $this->fiscal_year = $value;
    }

    // 月
    public function getFiscalmonthAttribute()
    {
        return $this->fiscal_month;
    }

    public function setFiscalmonthAttribute($value)
    {
        $this->fiscal_month = $value;
    }


    // 締日
    public function getClosingAttribute()
    {
        return $this->closing;
    }

    public function setClosingAttribute($value)
    {
        $this->closing = $value;
    }


    // 上限残業時間
    public function getUplimittimeAttribute()
    {
        return $this->uplimit_time;
    }

    public function setUplimittimeAttribute($value)
    {
        $this->uplimit_time = $value;
    }


    // 法定上限残業時間
    public function getStatutoryuplimittimeAttribute()
    {
        return $this->statutory_uplimit_time;
    }

    public function setStatutoryuplimittimeAttribute($value)
    {
        $this->statutory_uplimit_time = $value;
    }


    // 時間単位
    public function getTimeunitAttribute()
    {
        return $this->time_unit;
    }

    public function setTimeunitAttribute($value)
    {
        $this->time_unit = $value;
    }


    // 時間の丸め
    public function getTimeroundingAttribute()
    {
        return $this->time_rounding;
    }

    public function setTimeroundingAttribute($value)
    {
        $this->time_rounding = $value;
    }


    // 集計自動起動時刻
    public function getCalcautotimeAttribute()
    {
        return $this->calc_auto_time;
    }

    public function setCalcautotimeAttribute($value)
    {
        $this->calc_auto_time = $value;
    }


    // １ヶ月累計
    public function getMax1MonthtotalAttribute()
    {
        return $this->max_1month_total;
    }

    public function setMax1MonthtotalAttribute($value)
    {
        $this->max_1month_total = $value;
    }

    // ２ヶ月累計
    public function getMax2MonthtotalAttribute()
    {
        return $this->max_2month_total;
    }

    public function setMax2MonthtotalAttribute($value)
    {
        $this->max_2month_total = $value;
    }

    // ３ヶ月累計
    public function getMax3MonthtotalAttribute()
    {
        return $this->max_3month_total;
    }

    public function setMax3MonthtotalAttribute($value)
    {
        $this->max_3month_total = $value;
    }


    // ６ヶ月累計
    public function getMax6MonthtotalAttribute()
    {
        return $this->max_6month_total;
    }

    public function setMax6MonthtotalAttribute($value)
    {
        $this->max_6month_total = $value;
    }


    // １年間累計
    public function getMax12MonthtotalAttribute()
    {
        return $this->max_12month_total;
    }

    public function setMax12MonthtotalAttribute($value)
    {
        $this->max_12month_total = $value;
    }


    // ２－６ヶ月平均（特別条項）
    public function getAve26timespAttribute()
    {
        return $this->ave_2_6_time_sp;
    }

    public function setAve26timespAttribute($value)
    {
        $this->ave_2_6_time_sp = $value;
    }


    // １２ヶ月累計（特別条項）
    public function getMax12MonthtotalspAttribute()
    {
        return $this->max_12month_total_sp;
    }

    public function setMax12MonthtotalspAttribute($value)
    {
        $this->max_12month_total_sp = $value;
    }


    // １ヶ月累計（特別条項）
    public function getMax1MonthtotalspAttribute()
    {
        return $this->max_1month_total_sp;
    }

    public function setMax1MonthtotalspAttribute($value)
    {
        $this->max_1month_total_sp = $value;
    }

    // 特別条項の回数
    public function getCountspAttribute()
    {
        return $this->count_sp;
    }

    public function setCountspAttribute($value)
    {
        $this->count_sp = $value;
    }


    // 期首月
    public function getBeginningmonthAttribute()
    {
        return $this->beginning_month;
    }

    public function setBeginningmonthAttribute($value)
    {
        $this->beginning_month = $value;
    }

    // インターバル
    public function getIntervalAttribute()
    {
        return $this->interval;
    }

    public function setIntervalAttribute($value)
    {
        $this->interval = $value;
    }


    // 年
    public function getYearAttribute()
    {
        return $this->year;
    }

    public function setYearAttribute($value)
    {
        $this->year = $value;
    }


    // 作成ユーザー
    public function getCreateduserAttribute()
    {
        return $this->created_user;
    }

    public function setCreateduserAttribute($value)
    {
        $this->created_user = $value;
    }


    // 修正ユーザー
    public function getUpdateduserAttribute()
    {
        return $this->updated_user;
    }

    public function setUpdateduserAttribute($value)
    {
        $this->updated_user = $value;
    }


    // 削除フラグ
    public function getIsdeletedAttribute()
    {
        return $this->is_deleted;
    }

    public function setIsdeletedAttribute($value)
    {
        $this->is_deleted = $value;
    }
     
    public function getCreatedatAttribute()
    {
        return $this->created_at;
    }

    public function setCreatedatAttribute($value)
    {
        $this->created_at = $value;
    }

    public function getUpdatedatAttribute()
    {
        return $this->updated_at;
    }

    public function setUpdatedatAttribute($value)
    {
        $this->updated_at = $value;
    }

    //--------------- パラメータ項目属性 -----------------------------------

    private $param_account_id;                  // アカウントID
    private $param_fiscal_year;                 // 年度
    private $param_fiscal_month;                // 月
    private $param_year;                        // 年

    private $massegedata;                       // メッセージ

    // アカウントID
    public function getParamAccountidAttribute()
    {
        return $this->param_account_id;
    }

    public function setParamAccountidAttribute($value)
    {
        $this->param_account_id = $value;
    }

    // 年度
    public function getParamFiscalyearAttribute()
    {
        return $this->param_fiscal_year;
    }

    public function setParamFiscalyearAttribute($value)
    {
        $this->param_fiscal_year = $value;
    }

    // 月
    public function getParamFiscalmonthAttribute()
    {
        return $this->param_fiscal_month;
    }

    public function setParamFiscalmonthAttribute($value)
    {
        $this->param_fiscal_month = $value;
    }

    // 年
    public function getParamYearAttribute()
    {
        return $this->param_year;
    }

    public function setParamYearAttribute($value)
    {
        $this->param_year = $value;
    }

    // メッセージ
    public function getMassegedataAttribute()
    {
        return $this->massegedata;
    }

    public function setMassegedataAttribute($value)
    {
        $this->massegedata = $value;
    }


    // --------------------- メソッド ------------------------------------------------------

    /**
     * 設定項目マスタ取得
     *
     *      指定した年度、月度、年をもとに設定項目マスタを取得する
     *
     * @return sql取得結果
     */
    public function getSettingDatas(){

        try {
            // 取得SQL作成
            $mainquery = DB::table($this->table)
                ->select(
                    $this->table.'.account_id',
                    $this->table.'.fiscal_year',
                    $this->table.'.fiscal_month',
                    $this->table.'.closing',
                    $this->table.'.uplimit_time',
                    $this->table.'.statutory_uplimit_time',
                    $this->table.'.time_unit',
                    $this->table.'.time_rounding',
                    $this->table.'.calc_auto_time',
                    $this->table.'.max_1month_total',
                    $this->table.'.max_2month_total',
                    $this->table.'.max_3month_total',
                    $this->table.'.max_6month_total',
                    $this->table.'.max_12month_total',
                    $this->table.'.ave_2_6_time_sp',
                    $this->table.'.max_12month_total_sp',
                    $this->table.'.max_1month_total_sp',
                    $this->table.'.count_sp',
                    $this->table.'.beginning_month',
                    $this->table.'.interval',
                    $this->table.'.year',
                    $this->table.'.is_deleted');

            if(!empty($this->param_fiscal_year)){
                $mainquery->where($this->table.'.fiscal_year', $this->param_fiscal_year);           //年度指定
            }
            
            if(!empty($this->param_fiscal_month)){
                $mainquery->where($this->table.'.fiscal_month', $this->param_fiscal_month);         //月指定
            }
            
            if(!empty($this->param_year)){
                $mainquery->where($this->table.'.year', $this->param_year);                         //年指定
            }

            $data = $mainquery
                ->where($this->table.'.account_id', '=', $this->param_account_id)
                ->where($this->table.'.is_deleted', '=', 0)
                ->get();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

        return $data;
    }

    /**
     * 設定項目マスタ取得
     *
     *      指定した年をもとに年月淳に設定項目マスタを取得する
     *
     * @return sql取得結果
     */
    public function getSettingDatasYearOrderBy($orderby){

        try {
            // 取得SQL作成
            $sunquery1 = DB::table($this->table)
                ->select(
                    DB::raw('MAX('.$this->table.'.fiscal_year) as fiscal_year')
                );
            if(!empty($this->param_year)){
                $sunquery1->where($this->table.'.year', $this->param_year);                         //年指定
            }
            $sunquery1
                ->where($this->table.'.account_id', '=', $this->param_account_id)
                ->where($this->table.'.is_deleted', '=', 0)
                ->groupBy($this->table.'.year');

            $mainquery = DB::table($this->table)
                ->select(
                    $this->table.'.account_id',
                    $this->table.'.fiscal_year',
                    $this->table.'.fiscal_month',
                    $this->table.'.closing',
                    $this->table.'.uplimit_time',
                    $this->table.'.statutory_uplimit_time',
                    $this->table.'.time_unit',
                    $this->table.'.time_rounding',
                    $this->table.'.calc_auto_time',
                    $this->table.'.max_1month_total',
                    $this->table.'.max_2month_total',
                    $this->table.'.max_3month_total',
                    $this->table.'.max_6month_total',
                    $this->table.'.max_12month_total',
                    $this->table.'.ave_2_6_time_sp',
                    $this->table.'.max_12month_total_sp',
                    $this->table.'.max_1month_total_sp',
                    $this->table.'.count_sp',
                    $this->table.'.beginning_month',
                    $this->table.'.interval',
                    $this->table.'.year',
                    $this->table.'.is_deleted')
                ->JoinSub($sunquery1, 't2', function ($join) { 
                    $join->on('t2.fiscal_year', '=', $this->table.'.fiscal_year');
                })
                ->where($this->table.'.account_id', '=', $this->param_account_id)
                ->where($this->table.'.is_deleted', '=', 0);

            if (isset($orderby)) {
                if ($orderby == 1) {
                    $mainquery
                        ->orderBy($this->table.'.year', 'asc');
                } else {
                    $mainquery
                        ->orderBy($this->table.'.fiscal_year', 'asc');
                }
            }
            $get = $mainquery
                ->orderBy($this->table.'.fiscal_month', 'asc')
                ->get();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
        return $get;
    }

    /**
     * 締め日取得
     *
     *      指定した年月度の締め日を取得する
     *
     * @return sql取得結果
     */
    public function getMonthClosing(){
        
        // 取得
        try {
            return $mainquery = DB::table($this->table)
                ->where($this->table.'.account_id', '=', $this->param_account_id)
                ->where($this->table.'.year', $this->param_year)
                ->where($this->table.'.fiscal_month', $this->param_fiscal_month)
                ->where($this->table.'.is_deleted', 0)
                ->value($this->table.'.closing');
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

    }

    /**
     * 期首月取得
     *
     *      指定した年月度の期首月を取得する
     *
     * @return sql取得結果
     */
    public function getBeginingMonth(){

        try {
            // 取得
            return $mainquery = DB::table($this->table)
                ->where($this->table.'.account_id', '=', $this->param_account_id)
                ->where($this->table.'.year', $this->param_year)
                ->where($this->table.'.is_deleted', 0)
                ->value($this->table.'.beginning_month');
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

    }

    public function insertSettings(){
        try {
            DB::table($this->table)->insert(
                [
                    'account_id' => $this->account_id,
                    'fiscal_year' => $this->fiscal_year,
                    'fiscal_month' => $this->fiscal_month,
                    'closing' => $this->closing,
                    'uplimit_time' => $this->uplimit_time,
                    'time_unit' => $this->time_unit,
                    'time_rounding' => $this->time_rounding,
                    'calc_auto_time' => $this->calc_auto_time,
                    'max_1month_total' => $this->max_1month_total,
                    'max_2month_total' => $this->max_2month_total,
                    'max_3month_total' => $this->max_3month_total,
                    'max_6month_total' => $this->max_6month_total,
                    'max_12month_total' => $this->max_12month_total,
                    'ave_2_6_time_sp' => $this->ave_2_6_time_sp,
                    'max_12month_total_sp' => $this->max_12month_total_sp,
                    'max_1month_total_sp' => $this->max_1month_total_sp,
                    'count_sp' => $this->count_sp,
                    'beginning_month' => $this->beginning_month,
                    'interval' => $this->interval,
                    'year' => $this->year,
                    'created_user' => $this->created_user,
                    'created_at'=>$this->created_at
                ]
            );
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_insert_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_insert_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 存在チェック
     *
     * @return boolean
     */
    public function isExistsSetting(){
        try {
            $is_exists = DB::table($this->table)
                ->where($this->table.'.account_id', '=', $this->param_account_id)
                ->where('fiscal_year',$this->fiscal_year)
                ->where('is_deleted',0)
                ->exists();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_exists_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_exists_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

        return $is_exists;
    }

    /**
     * 詳細取得
     *
     * @return void
     */
    public function getDetails(){
        try {
            $details = DB::table($this->table)
                ->where($this->table.'.account_id', '=', $this->param_account_id)
                ->where('fiscal_year',$this->fiscal_year)
                ->where('is_deleted',0)
                ->get();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

        return $details;
    }

    /**
     * date削除
     *
     * @return void
     */
    public function delSetting(){
        try {
            DB::table($this->table)
                ->where($this->table.'.account_id', '=', $this->param_account_id)
                ->where('fiscal_year',$this->fiscal_year)
                ->delete();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_delete_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_delete_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

}
