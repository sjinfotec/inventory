<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
    private $fiscal_year;                   // 年度
    private $fiscal_month;                  // 月
    private $closing;                       // 締日
    private $uplimit_time;                  // 上限残業時間
    private $statutory_uplimit_time;        // 法定上限残業時間
    private $time_unit;                     // 時間単位
    private $time_rounding;                 // 時間の丸め
    private $max_3month_total;              // ３ヶ月累計
    private $max_6month_total;              // ６ヶ月累計
    private $max_12month_total;             // １年間累計
    private $beginning_month;               // 期首月
    private $year;                          // 年
    private $created_user;                  // 作成ユーザー
    private $updated_user;                  // 修正ユーザー
    private $is_deleted;                    // 削除フラグ

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


    // 期首月
    public function getBeginningmonthAttribute()
    {
        return $this->beginning_month;
    }

    public function setBeginningmonthAttribute($value)
    {
        $this->beginning_month = $value;
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


    //--------------- パラメータ項目属性 -----------------------------------

    private $param_fiscal_year;                 // 年度
    private $param_fiscal_month;                // 月
    private $param_year;                        // 年

    private $massegedata;                       // メッセージ

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


        // 取得SQL作成
        $mainquery = DB::table($this->table)
            ->select(
                $this->table.'.fiscal_year',
                $this->table.'.fiscal_month',
                $this->table.'.closing',
                $this->table.'.uplimit_time',
                $this->table.'.statutory_uplimit_time',
                $this->table.'.time_unit',
                $this->table.'.time_rounding',
                $this->table.'.max_3month_total',
                $this->table.'.max_6month_total',
                $this->table.'.max_12month_total',
                $this->table.'.beginning_month',
                $this->table.'.year',
                $this->table.'.is_deleted');

        if(!empty($this->param_fiscal_year)){
        $mainquery->where($this->table.'.fiscal_year', $this->param_fiscal_year);               //年度指定
        }
        
        if(!empty($this->param_fiscal_month)){
            $mainquery->where($this->table.'.fiscal_month', $this->param_fiscal_month);         //月指定
        }
        
        if(!empty($this->param_year)){
            $mainquery->where($this->table.'.year', $this->param_year);                         //年指定
        }

        $data = $mainquery->where('t1.is_deleted', '=', 0)
            ->get();

        return $data;
    }

}
