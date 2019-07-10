<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\WorkingTimedate;
use App\WorkTime;

/**
 * 労働時間計算（日次）
 * 
 * テーブル：タイムレコード（work_times）より、
 * 労働時間を計算してテーブル：日次タイムレコード（working_time_date）
 * を作成する
 *
 *      可能範囲指定：
 *          ①指定部署（省略可：全部署対象）
 *          ②指定ユーザー（省略可：全ユーザー対象）
 *          ③開始計算日付から終了計算日付
 *
 *      使用方法：
 *          ①範囲指定プロパティを事前設定（設定しない場合はerrorとするので注意）
 *          ②メソッド：calcWorkingTimeDateを実行
 *
 * @author      o.shindo
 * @version     1.00    20190629
*/
class WorkingTimeDateCalcController extends Controller
{
    //
    private $department_code;           // 指定部署

    // 指定部署
    public function getDepartmentcodeAttribute()
    {
        return $this->department_code;
    }

    public function setDepartmentcodeAttribute($value)
    {
        $this->department_code = $value;
    }
    //
    private $user_code;                 // 指定ユーザー

    // 指定ユーザー
    public function getUsercodeAttribute()
    {
        return $this->user_code;
    }

    public function setUsercodeAttribute($value)
    {
        $this->user_code = $value;
    }

    private $date_from;                 // 開始計算日付

    // 開始計算日付
    public function getDatefromAttribute()
    {
        $date = new DateTime($this->date_from);
        return $date->format('Ymd');
    }

    public function setDatefromAttribute($value)
    {
        $this->date_from = $value;
    }

    private $date_to;                 // 終了計算日付

    // 終了計算日付
    public function getDatetoAttribute()
    {
        $date = new DateTime($this->date_to);
        return $date->format('Ymd');
    }

    public function setDatetoAttribute($value)
    {
        $this->date_to = $value;
    }

    private $messageout;                 // メッセージ

    // メッセージ
    public function getMessageoutAttribute()
    {
        return $this->messageout;
    }

    public function setMessageoutAttribute($value)
    {
        $this->messageout = $value;
    }


    
    /**
     * コンストラクタ
     *
     */
    public function __construct()
    {
        $this->messageout="";
    }


    /**
     * 労働時間計算（日次）
     *  テーブル：タイムレコード（work_times）より、
     *  労働時間を計算してテーブル：日次タイムレコード（working_time_date）
     *  を作成する
     *
     * @return bool  true:正常
     */
    public function calcWorkingTimeDate()
    {
        // json形式でresponceする
        //$results = collect();       // 処理結果をコレクションで初期化

        $this->messageout="";
        //  パラメータチェック
        /*if(!chkParam()) {
            $results->put(
                'datas', null
                'messageout', $this->messageout
            );
            // コレクションをjson化して返却
            //return $results->toJson();
            return null;
        }

        try {
          // 対象ユーザーの範囲で計算開始
            $WorkTime_model = new WorkTime();
            $WorkTime_model->setParamUsercodeAttribute($this->user_code);
            $WorkTime_model->setParamDepartmentcodeAttribute($this->department_code);
            $WorkTime_model->setDatefromAttribute($this->date_from);
            $WorkTime_model->setDatetoAttribute($this->date_to);
            $WorkTime_model->setIsdeletedAttribute(0);
            $WorkTime_data = $WorkTime_model->getWorkingTimeData();      // 労働時間取得
            foreach ($WorkTime_data as $result) {
                $WorkingTimedate_model = new WorkingTimedate();
                break;
            }
        } catch (Exception $e) {
            if ($this->messageout != "") {$this->messageout .= "\r\n";}
            $this->messageout .= $e->getMessage();
            return false;
        } */

    }

    /**
     * パラメータチェック
     * 
     *      チェック内容
     *          ①必須チェック   計算日付
     *          ①大小チェック   ユーザー、計算日付
     *
     * @return bool  true:正常
     */
    private function chkParam()
    {
        $result = true;
        /*if(!isset($this->user_code)){
            $this->user_code = '';
        }
        if(!isset($this->department_code)){
            $this->department_code = '';
        }
        if(!isset($this->date_from)){
            if ($this->messageout != "") {$this->messageout .= "\r\n";}
            $this->messageout .= "開始計算日付ーが未設定";
            $result = false;
        }
        if(!isset($this->date_to)){
            if ($this->messageout != "") {$this->messageout .= "\r\n";}
            $this->messageout .= "終了計算日付ーが未設定";
            $result = false;
        }

        if ($result) {
            // 計算日付範囲が不正はerror
            if($this->date_from > $this->date_to){
                if ($this->messageout != "") {$this->messageout .= "\r\n";}
                $this->messageout .= "開始計算日付　＞　終了計算日付";
                $result = false;
            }
        }*/
        return $result;
    }

}
