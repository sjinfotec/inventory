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
 *          ①開始ユーザーから終了ユーザー（省略可：全ユーザー対象）
 *          ②開始計算日付から終了計算日付
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
    private $user_code_from;                 // 開始ユーザー

    // 開始ユーザー
    public function getUsercodefromAttribute()
    {
        return $this->user_code_from;
    }

    public function setUsercodefromAttribute($value)
    {
        $this->user_code_from = $value;
    }

    private $user_code_to;                 // 終了ユーザー

    // 終了ユーザー
    public function getUsercodetoAttribute()
    {
        return $this->user_code_to;
    }

    public function setUsercodetoAttribute($value)
    {
        $this->user_code_to = $value;
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
        $this->messageout="";
        if(!chkParam) { return false }  //  パラメータチェック

        try {
            // 対象ユーザーの範囲で計算開始
            $WorkTime_model = new WorkTime();
            $WorkTime_model->setUsercodefromAttribute($this->user_code_from);
            $WorkTime_model->setUsercodetoAttribute($this->user_code_to);
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
            return false
        }


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
        if(!isset($this->user_code_from)){
            $this->user_code_from = 0;
        }
        if(!isset($this->user_code_to)){
            $this->user_code_to = 9999999999;
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
            // ユーザー範囲が不正はerror
            if($this->user_code_from > $this->user_code_to){
                if ($this->messageout != "") {$this->messageout .= "\r\n";}
                $this->messageout .= "開始ユーザー　＞　終了ユーザー";
                $result = false;
            }
            // ユーザー範囲が不正はerror
            if($this->date_from > $this->date_to){
                if ($this->messageout != "") {$this->messageout .= "\r\n";}
                $this->messageout .= "開始計算日付　＞　終了計算日付";
                $result = false;
            }
        }
        return $result;
    }

}
