<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ApiCommonController;

class TempWorkingTimeDate extends Model
{
    protected $table = 'temp_working_time_dates';
    protected $table_users = 'users';
    protected $table_departments = 'departments';
    protected $table_generalcodes = 'generalcodes';
    protected $guarded = array('id');

    //--------------- 項目属性 -----------------------------------

    private $working_date;                  // 日付
    private $employment_status;             // 雇用形態
    private $department_code;               // 部署ID
    private $user_code;                     // ユーザー
    private $seq;                           // 順位
    private $employment_status_name;        // 雇用形態名称
    private $department_name;               // 部署名称
    private $user_name;                     // ユーザー名称
    private $working_timetable_no;          // タイムテーブルNo
    private $working_timetable_name;        // タイムテーブル名称
    private $array_attendance_time = [null,null,null,null,null];                    // 出勤時刻
    private $array_attendance_time_positions = [null,null,null,null,null];          // 出勤位置情報
    private $array_attendance_time_id = [null,null,null,null,null];                 // 出勤打刻時刻テーブルID
    private $array_attendance_editor_department_code = [null,null,null,null,null];  // 出勤編集部署コード
    private $array_attendance_editor_user_code = [null,null,null,null,null];        // 出勤編集ユーザーコード
    private $array_attendance_editor_department_name = [null,null,null,null,null];  // 出勤編集部署名
    private $array_attendance_editor_user_name = [null,null,null,null,null];        // 出勤編集ユーザー名
    private $array_leaving_time = [null,null,null,null,null];                       // 退勤時刻
    private $array_leaving_time_positions = [null,null,null,null,null];             // 退勤位置情報
    private $array_leaving_time_id = [null,null,null,null,null];                    // 退勤打刻時刻テーブルID
    private $array_leaving_editor_department_code = [null,null,null,null,null];     // 退勤編集部署コード
    private $array_leaving_editor_user_code = [null,null,null,null,null];           // 退勤編集ユーザーコード
    private $array_leaving_editor_department_name = [null,null,null,null,null];     // 退勤編集部署名
    private $array_leaving_editor_user_name = [null,null,null,null,null];           // 退勤編集ユーザー名
    private $array_missing_middle_time = [null,null,null,null,null];                // 私用外出時刻
    private $array_missing_middle_time_positions = [null,null,null,null,null];      // 私用外出位置情報
    private $array_missing_middle_time_id = [null,null,null,null,null];             // 私用外出打刻時刻テーブルID
    private $array_missing_editor_department_code = [null,null,null,null,null];     // 私用外出編集部署コード
    private $array_missing_editor_user_code = [null,null,null,null,null];           // 私用外出編集ユーザーコード
    private $array_missing_editor_department_name = [null,null,null,null,null];     // 私用外出編集部署名
    private $array_missing_editor_user_name = [null,null,null,null,null];           // 私用外出編集ユーザー名
    private $array_missing_middle_return_time = [null,null,null,null,null];                 // 私用外出戻り時刻
    private $array_missing_middle_return_time_positions = [null,null,null,null,null];       // 私用外出戻り位置情報
    private $array_missing_middle_return_time_id = [null,null,null,null,null];              // 私用外出戻り打刻時刻テーブルID
    private $array_missing_return_editor_department_code = [null,null,null,null,null];      // 私用外出戻り編集部署コード
    private $array_missing_return_editor_user_code = [null,null,null,null,null];            // 私用外出戻り編集ユーザーコード
    private $array_missing_return_editor_department_name = [null,null,null,null,null];      // 私用外出戻り編集部署名
    private $array_missing_return_editor_user_name = [null,null,null,null,null];            // 私用外出戻り編集ユーザー名
    private $array_public_going_out_time = [null,null,null,null,null,null,null];                      // 公用外出時刻
    private $array_public_going_out_time_positions = [null,null,null,null,null,null,null];            // 公用外出位置情報
    private $array_public_going_out_time_id = [null,null,null,null,null,null,null];                   // 公用外出打刻時刻テーブルID
    private $array_public_editor_department_code = [null,null,null,null,null,null,null];              // 公用外出編集部署コード
    private $array_public_editor_user_code = [null,null,null,null,null,null,null];                    // 公用外出編集ユーザーコード
    private $array_public_editor_department_name = [null,null,null,null,null,null,null];              // 公用外出編集部署名
    private $array_public_editor_user_name = [null,null,null,null,null,null,null];                    // 公用外出編集ユーザー名
    private $array_public_going_out_return_time = [null,null,null,null,null,null,null];               // 公用外出戻り時刻
    private $array_public_going_out_return_time_positions = [null,null,null,null,null,null,null];     // 公用外出戻り位置情報
    private $array_public_going_out_return_time_id = [null,null,null,null,null,null,null];            // 公用外出戻り打刻時刻テーブルID
    private $array_public_return_editor_department_code = [null,null,null,null,null,null,null];       // 公用外出戻り編集部署コード
    private $array_public_return_editor_user_code = [null,null,null,null,null,null,null];             // 公用外出戻り編集ユーザーコード
    private $array_public_return_editor_department_name = [null,null,null,null,null,null,null];       // 公用外出戻り編集部署名
    private $array_public_return_editor_user_name = [null,null,null,null,null,null,null];             // 公用外出戻り編集ユーザー名
    private $total_working_times;           // 合計勤務時間
    private $regular_working_times;         // 所定労働時間
    private $out_of_regular_working_times;  // 所定外労働時間
    private $overtime_hours;                // 残業時間
    private $late_night_overtime_hours;     // 深夜残業時間
    private $late_night_working_hours;      // 深夜労働時間
    private $legal_working_times;           // 法定労働時間
    private $out_of_legal_working_times;    // 法定外労働時間
    private $not_employment_working_hours;  // 未就労労働時間
    private $off_hours_working_hours;       // 時間外労働時間
    private $missing_middle_hours;          // 私用外出時間
    private $public_going_out_hours;        // 公用外出時間
    private $out_of_legal_working_holiday_hours;                    // 法定外休日労働時間
    private $out_of_legal_working_holiday_night_overtime_hours;     // 法定外休日深夜残業時間
    private $legal_working_holiday_hours;                   // 法定休日労働時間
    private $legal_working_holiday_night_overtime_hours;    // 法定休日深夜残業時間
    private $working_status;                // 勤務状態
    private $working_status_name;           // 勤務状態名称
    private $note;                          // メモ
    private $late;                          // 遅刻有無
    private $leave_early;                   // 早退有無
    private $current_calc;                  // 当日計算有無
    private $to_be_confirmed;               // 要確認有無
    private $weekday_kubun;                 // 曜日区分
    private $weekday_name;                  // 曜日名称
    private $business_kubun;                // 営業日区分
    private $business_name;                 // 営業日名称
    private $holiday_kubun;                 // 休暇区分
    private $holiday_name;                  // 休暇名称
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
    private $pattern;                       // 打刻パターン
    private $check_result;                  // 打刻チェック結果
    private $check_max_times;               // 打刻回数最大チェック結果
    private $check_interval;                // インターバルチェック結果
    private $positions;                     // 位置情報
    private $fixedtime;                     // 確定
    private $systemdate;

    // 日付
    public function getWorkingdateAttribute()
    {
        return $this->working_date;
    }

    public function setWorkingdateAttribute($value)
    {
        $this->working_date = $value;
    }


    // 雇用形態
    public function getEmploymentstatusAttribute()
    {
        return $this->employment_status;
    }

    public function setEmploymentstatusAttribute($value)
    {
        $this->employment_status = $value;
    }


    // 部署ID
    public function getDepartmentcodeAttribute()
    {
        return $this->department_code;
    }

    public function setDepartmentcodeAttribute($value)
    {
        $this->department_code = $value;
    }

    // ユーザー
    public function getUsercodeAttribute()
    {
        return $this->user_code;
    }

    public function setUsercodeAttribute($value)
    {
        $this->user_code = $value;
    }

    // ユーザー
    public function getSeqAttribute()
    {
        return $this->seq;
    }

    public function setSeqAttribute($value)
    {
        $this->seq = $value;
    }

    // 雇用形態名称
    public function getEmploymentstatusnameAttribute()
    {
        return $this->employment_status_name;
    }

    public function setEmploymentstatusnameAttribute($value)
    {
        $this->employment_status_name = $value;
    }


    // 部署名称
    public function getDepartmentnameAttribute()
    {
        return $this->department_name;
    }

    public function setDepartmentnameAttribute($value)
    {
        $this->department_name = $value;
    }


    // ユーザー名称
    public function getUsernameAttribute()
    {
        return $this->user_name;
    }

    public function setUsernameAttribute($value)
    {
        $this->user_name = $value;
    }


    // タイムテーブルNo
    public function getWorkingtimetablenoAttribute()
    {
        return $this->working_timetable_no;
    }

    public function setWorkingtimetablenoAttribute($value)
    {
        $this->working_timetable_no = $value;
    }


    // タイムテーブル名称
    public function getWorkingtimetablenameAttribute()
    {
        return $this->working_timetable_name;
    }

    public function setWorkingtimetablenameAttribute($value)
    {
        $this->working_timetable_name = $value;
    }


    // 出勤時刻
    public function getAttendancetimeAttribute($index)
    {
        return $this->array_attendance_time[$index];
    }

    public function setAttendancetimeAttribute($index, $value)
    {
        $this->array_attendance_time[$index] = $value;
    }

    // 出勤位置情報
    public function getAttendancetimepositionsAttribute($index)
    {
        return $this->array_attendance_time_positions[$index];
    }

    public function setAttendancetimepositionsAttribute($index, $value)
    {
        $this->array_attendance_time_positions[$index] = $value;
    }

    // 出勤打刻時刻テーブルID
    public function getAttendancetimeidAttribute($index)
    {
        return $this->array_attendance_time_id[$index];
    }

    public function setAttendancetimeidAttribute($index, $value)
    {
        if ($value == "") {
            $this->array_attendance_time_id[$index] = null;
        } else {
            $this->array_attendance_time_id[$index] = $value;
        }
    }

    // 出勤編集部署コード
    public function getAttendanceeditordepartmentcodeAttribute($index)
    {
        return $this->array_attendance_editor_department_code[$index];
    }

    public function setAttendanceeditordepartmentcodeAttribute($index, $value)
    {
        $this->array_attendance_editor_department_code[$index] = $value;
    }

    // 出勤編集ユーザーコード
    public function getAttendanceeditorusercodeAttribute($index)
    {
        return $this->array_attendance_editor_user_code[$index];
    }

    public function setAttendanceeditorusercodeAttribute($index, $value)
    {
        $this->array_attendance_editor_user_code[$index] = $value;
    }

    // 出勤編集部署名
    public function getAttendanceeditordepartmentnameAttribute($index)
    {
        return $this->array_attendance_editor_department_name[$index];
    }

    public function setAttendanceeditordepartmentnameAttribute($index, $value)
    {
        $this->array_attendance_editor_department_name[$index] = $value;
    }

    // 出勤編集ユーザー名
    public function getAttendanceeditorusernameAttribute($index)
    {
        return $this->array_attendance_editor_user_name[$index];
    }

    public function setAttendanceeditorusernameAttribute($index, $value)
    {
        $this->array_attendance_editor_user_name[$index] = $value;
    }

    // 退勤時刻
    public function getLeavingtimeAttribute($index)
    {
        return $this->array_leaving_time[$index];
    }

    public function setLeavingtimeAttribute($index, $value)
    {
        $this->array_leaving_time[$index] = $value;
    }

    // 退勤位置情報
    public function getLeavingtimepositionsAttribute($index)
    {
        return $this->array_leaving_time_positions[$index];
    }

    public function setLeavingtimepositionsAttribute($index, $value)
    {
        $this->array_leaving_time_positions[$index] = $value;
    }

    // 退勤打刻時刻テーブルID
    public function getLeavingtimeidAttribute($index)
    {
        return $this->array_leaving_time_id[$index];
    }

    public function setLeavingtimeidAttribute($index, $value)
    {
        if ($value == "") {
            $this->array_leaving_time_id[$index] = null;
        } else {
            $this->array_leaving_time_id[$index] = $value;
        }
    }

    // 退勤編集部署コード
    public function getLeavingeditordepartmentcodeAttribute($index)
    {
        return $this->array_leaving_editor_department_code[$index];
    }

    public function setLeavingeditordepartmentcodeAttribute($index, $value)
    {
        $this->array_leaving_editor_department_code[$index] = $value;
    }

    // 退勤編集ユーザーコード
    public function getLeavingeditorusercodeAttribute($index)
    {
        return $this->array_leaving_editor_user_code[$index];
    }

    public function setLeavingeditorusercodeAttribute($index, $value)
    {
        $this->array_leaving_editor_user_code[$index] = $value;
    }

    // 退勤編集部署名
    public function getLeavingeditordepartmentnameAttribute($index)
    {
        return $this->array_leaving_editor_department_name[$index];
    }

    public function setLeavingeditordepartmentnameAttribute($index, $value)
    {
        $this->array_leaving_editor_department_name[$index] = $value;
    }

    // 退勤編集ユーザー名
    public function getLeavingeditorusernameAttribute($index)
    {
        return $this->array_leaving_editor_user_name[$index];
    }

    public function setLeavingeditorusernameAttribute($index, $value)
    {
        $this->array_leaving_editor_user_name[$index] = $value;
    }

    // 私用外出時刻
    public function getMissingmiddletimeAttribute($index)
    {
        return $this->array_missing_middle_time[$index];
    }

    public function setMissingmiddletimeAttribute($index, $value)
    {
        $this->array_missing_middle_time[$index] = $value;
    }

    // 私用外出位置情報
    public function getMissingmiddletimepositionsAttribute($index)
    {
        return $this->array_missing_middle_time_positions[$index];
    }

    public function setMissingmiddletimepositionsAttribute($index, $value)
    {
        $this->array_missing_middle_time_positions[$index] = $value;
    }

    // 私用外出打刻時刻テーブルID
    public function getMissingmiddletimeidAttribute($index)
    {
        return $this->array_missing_middle_time_id[$index];
    }

    public function setMissingmiddletimeidAttribute($index, $value)
    {
        if ($value == "") {
            $this->array_missing_middle_time_id[$index] = null;
        } else {
            $this->array_missing_middle_time_id[$index] = $value;
        }
    }

    // 私用外出編集部署コード
    public function getMissingeditordepartmentcodeAttribute($index)
    {
        return $this->array_missing_editor_department_code[$index];
    }

    public function setMissingeditordepartmentcodeAttribute($index, $value)
    {
        $this->array_missing_editor_department_code[$index] = $value;
    }

    // 私用外出編集ユーザーコード
    public function getMissingeditorusercodeAttribute($index)
    {
        return $this->array_missing_editor_user_code[$index];
    }

    public function setMissingeditorusercodeAttribute($index, $value)
    {
        $this->array_missing_editor_user_code[$index] = $value;
    }

    // 私用外出編集部署名
    public function getMissingeditordepartmentnameAttribute($index)
    {
        return $this->array_missing_editor_department_name[$index];
    }

    public function setMissingeditordepartmentnameAttribute($index, $value)
    {
        $this->array_missing_editor_department_name[$index] = $value;
    }

    // 私用外出編集ユーザー名
    public function getMissingeditorusernameAttribute($index)
    {
        return $this->array_missing_editor_user_name[$index];
    }

    public function setMissingeditorusernameAttribute($index, $value)
    {
        $this->array_missing_editor_user_name[$index] = $value;
    }

    // 私用外出戻り時刻
    public function getMissingmiddlereturntimeAttribute($index)
    {
        return $this->array_missing_middle_return_time[$index];
    }

    public function setMissingmiddlereturntimeAttribute($index, $value)
    {
        $this->array_missing_middle_return_time[$index] = $value;
    }

    // 私用外出戻り位置情報
    public function getMissingmiddlereturntimepositionsAttribute($index)
    {
        return $this->array_missing_middle_return_time_positions[$index];
    }

    public function setMissingmiddlereturntimepositionsAttribute($index, $value)
    {
        $this->array_missing_middle_return_time_positions[$index] = $value;
    }

    // 私用外出戻り打刻時刻テーブルID
    public function getMissingmiddlereturntimeidAttribute($index)
    {
        return $this->array_missing_middle_return_time_id[$index];
    }

    public function setMissingmiddlereturntimeidAttribute($index, $value)
    {
        if ($value == "") {
            $this->array_missing_middle_return_time_id[$index] = null;
        } else {
            $this->array_missing_middle_return_time_id[$index] = $value;
        }
    }

    // 私用外出戻り編集部署コード
    public function getMissingreturneditordepartmentcodeAttribute($index)
    {
        return $this->array_missing_return_editor_department_code[$index];
    }

    public function setMissingreturneditordepartmentcodeAttribute($index, $value)
    {
        $this->array_missing_return_editor_department_code[$index] = $value;
    }

    // 私用外出戻り編集ユーザーコード
    public function getMissingreturneditorusercodeAttribute($index)
    {
        return $this->array_missing_return_editor_user_code[$index];
    }

    public function setMissingreturneditorusercodeAttribute($index, $value)
    {
        $this->array_missing_return_editor_user_code[$index] = $value;
    }

    // 私用外出戻り編集部署名
    public function getMissingreturneditordepartmentnameAttribute($index)
    {
        return $this->array_missing_return_editor_department_name[$index];
    }

    public function setMissingreturneditordepartmentnameAttribute($index, $value)
    {
        $this->array_missing_return_editor_department_name[$index] = $value;
    }


    // 私用外出戻り編集ユーザー名
    public function getMissingreturneditorusernameAttribute($index)
    {
        return $this->array_missing_return_editor_user_name[$index];
    }

    public function setMissingreturneditorusernameAttribute($index, $value)
    {
        $this->array_missing_return_editor_user_name[$index] = $value;
    }

    // 公用外出時刻
    public function getPublicgoingouttimeAttribute($index)
    {
        return $this->array_public_going_out_time[$index];
    }

    public function setPublicgoingouttimeAttribute($index, $value)
    {
        $this->array_public_going_out_time[$index] = $value;
    }

    // 公用外出位置情報
    public function getPublicgoingouttimepositionsAttribute($index)
    {
        return $this->array_public_going_out_time_positions[$index];
    }

    public function setPublicgoingouttimepositionsAttribute($index, $value)
    {
        $this->array_public_going_out_time_positions[$index] = $value;
    }

    // 公用外出打刻時刻テーブルID
    public function getPublicgoingouttimeidAttribute($index)
    {
        return $this->array_public_going_out_time_id[$index];
    }

    public function setPublicgoingouttimeidAttribute($index, $value)
    {
        if ($value == "") {
            $this->array_public_going_out_time_id[$index] = null;
        } else {
            $this->array_public_going_out_time_id[$index] = $value;
        }
    }

    // 公用外出編集部署コード
    public function getPubliceditordepartmentcodeAttribute($index)
    {
        return $this->array_public_editor_department_code[$index];
    }

    public function setPubliceditordepartmentcodeAttribute($index, $value)
    {
        $this->array_public_editor_department_code[$index] = $value;
    }

    // 公用外出編集ユーザーコード
    public function getPubliceditorusercodeAttribute($index)
    {
        return $this->array_public_editor_user_code[$index];
    }

    public function setPubliceditorusercodeAttribute($index, $value)
    {
        $this->array_public_editor_user_code[$index] = $value;
    }

    // 公用外出編集部署名
    public function getPubliceditordepartmentnameAttribute($index)
    {
        return $this->array_public_editor_department_name[$index];
    }

    public function setPubliceditordepartmentnameAttribute($index, $value)
    {
        $this->array_public_editor_department_name[$index] = $value;
    }

    // 公用外出編集ユーザー名
    public function getPubliceditorusernameAttribute($index)
    {
        return $this->array_public_editor_user_name[$index];
    }

    public function setPubliceditorusernameAttribute($index, $value)
    {
        $this->array_public_editor_user_name[$index] = $value;
    }

    // 公用外出戻り時刻
    public function getPublicgoingoutreturntimeAttribute($index)
    {
        return $this->array_public_going_out_return_time[$index];
    }

    public function setPublicgoingoutreturntimeAttribute($index, $value)
    {
        $this->array_public_going_out_return_time[$index] = $value;
    }

    // 公用外出戻り位置情報
    public function getPublicgoingoutreturntimepositionsAttribute($index)
    {
        return $this->array_public_going_out_return_time_positions[$index];
    }

    public function setPublicgoingoutreturntimepositionsAttribute($index, $value)
    {
        $this->array_public_going_out_return_time_positions[$index] = $value;
    }

    // 公用外出戻り打刻時刻テーブルID
    public function getPublicgoingoutreturntimeidAttribute($index)
    {
        return $this->array_public_going_out_return_time_id[$index];
    }

    public function setPublicgoingoutreturntimeidAttribute($index, $value)
    {
        if ($value == "") {
            $this->array_public_going_out_return_time_id[$index] = null;
        } else {
            $this->array_public_going_out_return_time_id[$index] = $value;
        }
    }

    // 公用外出戻り編集部署コード
    public function getPublicreturneditordepartmentcodeAttribute($index)
    {
        return $this->array_public_return_editor_department_code[$index];
    }

    public function setPublicreturneditordepartmentcodeAttribute($index, $value)
    {
        $this->array_public_return_editor_department_code[$index] = $value;
    }

    // 公用外出戻り編集ユーザーコード
    public function getPublicreturneditorusercodeAttribute($index)
    {
        return $this->array_public_return_editor_user_code[$index];
    }

    public function setPublicreturneditorusercodeAttribute($index, $value)
    {
        $this->array_public_return_editor_user_code[$index] = $value;
    }

    // 公用外出戻り編集部署名
    public function getPublicreturneditordepartmentnameAttribute($index)
    {
        return $this->array_public_return_editor_department_name[$index];
    }

    public function setPublicreturneditordepartmentnameAttribute($index, $value)
    {
        $this->array_public_return_editor_department_name[$index] = $value;
    }

    // 公用外出戻り編集ユーザー名
    public function getPublicreturneditorusernameAttribute($index)
    {
        return $this->array_public_return_editor_user_name[$index];
    }

    public function setPublicreturneditorusernameAttribute($index, $value)
    {
        $this->array_public_return_editor_user_name[$index] = $value;
    }

    // 合計勤務時間
    public function getTotalworkingtimesAttribute()
    {
        return $this->total_working_times;
    }

    public function setTotalworkingtimesAttribute($value)
    {
        if ($value > 9999.99) {
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.$value);
            $value = 9999.99;
        }
        $this->total_working_times = $value;
    }


    // 所定労働時間
    public function getRegularworkingtimesAttribute()
    {
        return $this->regular_working_times;
    }

    public function setRegularworkingtimesAttribute($value)
    {
        if ($value > 9999.99) {
            $value = 9999.99;
        }
        $this->regular_working_times = $value;
    }


    // 所定外労働時間
    public function getOutofregularworkingtimesAttribute()
    {
        return $this->out_of_regular_working_times;
    }

    public function setOutofregularworkingtimesAttribute($value)
    {
        if ($value > 9999.99) {
            $value = 9999.99;
        }
        $this->out_of_regular_working_times = $value;
    }


    // 残業時間
    public function getOvertimehoursAttribute()
    {
        return $this->overtime_hours;
    }

    public function setOvertimehoursAttribute($value)
    {
        if ($value > 9999.99) {
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.$value);
            $value = 9999.99;
        }
        $this->overtime_hours = $value;
    }


    // 深夜残業時間
    public function getLatenightovertimehoursAttribute()
    {
        return $this->late_night_overtime_hours;
    }

    public function setLatenightovertimehoursAttribute($value)
    {
        if ($value > 9999.99) {
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.$value);
            $value = 9999.99;
        }
        $this->late_night_overtime_hours = $value;
    }


    // 深夜労働時間
    public function getLatenightworkinghoursAttribute()
    {
        return $this->late_night_working_hours;
    }

    public function setLatenightworkinghoursAttribute($value)
    {
        if ($value > 9999.99) {
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.$value);
            $value = 9999.99;
        }
        $this->late_night_working_hours = $value;
    }


    // 法定労働時間
    public function getLegalworkingtimesAttribute()
    {
        return $this->legal_working_times;
    }

    public function setLegalworkingtimesAttribute($value)
    {
        if ($value > 9999.99) {
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.$value);
            $value = 9999.99;
        }
        $this->legal_working_times = $value;
    }


    // 法定外労働時間
    public function getOutoflegalworkingtimesAttribute()
    {
        return $this->out_of_legal_working_times;
    }

    public function setOutoflegalworkingtimesAttribute($value)
    {
        if ($value > 9999.99) {
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.$value);
            $value = 9999.99;
        }
        $this->out_of_legal_working_times = $value;
    }


    // 未就労労働時間
    public function getNotemploymentworkinghoursAttribute()
    {
        return $this->not_employment_working_hours;
    }

    public function setNotemploymentworkinghoursAttribute($value)
    {
        if ($value > 9999.99) {
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.$value);
            $value = 9999.99;
        }
        $this->not_employment_working_hours = $value;
    }


    // 時間外労働時間
    public function getOffhoursworkinghoursAttribute()
    {
        return $this->off_hours_working_hours;
    }

    public function setOffhoursworkinghoursAttribute($value)
    {
        if ($value > 9999.99) {
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.$value);
            $value = 9999.99;
        }
        $this->off_hours_working_hours = $value;
    }

    // 私用外出時間
    public function getMissingmiddlehoursAttribute()
    {
        return $this->missing_middle_hours;
    }

    public function setMissingmiddlehoursAttribute($value)
    {
        if ($value > 9999.99) {
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.$value);
            $value = 9999.99;
        }
        $this->missing_middle_hours = $value;
    }

    // 公用外出時間
    public function getPublicgoingouthoursAttribute()
    {
        return $this->public_going_out_hours;
    }

    public function setPublicgoingouthoursAttribute($value)
    {
        if ($value > 9999.99) {
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.$value);
            $value = 9999.99;
        }
        $this->public_going_out_hours = $value;
    }

    // 法定外休日労働時間
    public function getOutoflegalworkingholidayhoursAttribute()
    {
        return $this->out_of_legal_working_holiday_hours;
    }

    public function setOutoflegalworkingholidayhoursAttribute($value)
    {
        if ($value > 9999.99) {
            $value = 9999.99;
        }
        $this->out_of_legal_working_holiday_hours = $value;
    }

    // 法定外休日深夜残業時間
    public function getOutoflegalworkingholidaynightovertimehoursAttribute()
    {
        return $this->out_of_legal_working_holiday_night_overtime_hours;
    }

    public function setOutoflegalworkingholidaynightovertimehoursAttribute($value)
    {
        if ($value > 9999.99) {
            $value = 9999.99;
        }
        $this->out_of_legal_working_holiday_night_overtime_hours = $value;
    }

    // 法定休日労働時間
    public function getLegalworkingholidayhoursAttribute()
    {
        return $this->legal_working_holiday_hours;
    }

    public function setLegalworkingholidayhoursAttribute($value)
    {
        if ($value > 9999.99) {
            $value = 9999.99;
        }
        $this->legal_working_holiday_hours = $value;
    }

    // 法定休日深夜残業時間
    public function getLegalworkingholidaynightovertimehoursAttribute()
    {
        return $this->legal_working_holiday_night_overtime_hours;
    }

    public function setLegalworkingholidaynightovertimehoursAttribute($value)
    {
        if ($value > 9999.99) {
            $value = 9999.99;
        }
        $this->legal_working_holiday_night_overtime_hours = $value;
    }
    

    // 勤務状態
    public function getWorkingstatusAttribute()
    {
        return $this->working_status;
    }

    public function setWorkingstatusAttribute($value)
    {
        $this->working_status = $value;
    }

    // 勤務状態名称
    public function getWorkingstatusnameAttribute()
    {
        return $this->working_status_name;
    }

    public function setWorkingstatusnameAttribute($value)
    {
        $this->working_status_name = $value;
    }


    // メモ
    public function getNoteAttribute()
    {
        return $this->note;
    }

    public function setNoteAttribute($value)
    {
        $this->note = $value;
    }


    // 遅刻有無
    public function getLateAttribute()
    {
        return $this->late;
    }

    public function setLateAttribute($value)
    {
        $this->late = $value;
    }


    // 早退有無
    public function getLeaveearlyAttribute()
    {
        return $this->leave_early;
    }

    public function setLeaveearlyAttribute($value)
    {
        $this->leave_early = $value;
    }


    // 当日計算有無
    public function getCurrentcalcAttribute()
    {
        return $this->current_calc;
    }

    public function setCurrentcalcAttribute($value)
    {
        $this->current_calc = $value;
    }


    // 要確認有無
    public function getTobeconfirmedAttribute()
    {
        return $this->to_be_confirmed;
    }

    public function setTobeconfirmedAttribute($value)
    {
        $this->to_be_confirmed = $value;
    }


    // 曜日区分
    public function getWeekdaykubunAttribute()
    {
        return $this->weekday_kubun;
    }

    public function setWeekdaykubunAttribute($value)
    {
        $this->weekday_kubun = $value;
    }


    // 曜日名称
    public function getWeekdaynameAttribute()
    {
        return $this->weekday_name;
    }

    public function setWeekdaynameAttribute($value)
    {
        $this->weekday_name = $value;
    }


    // 営業日区分
    public function getBusinesskubunAttribute()
    {
        return $this->business_kubun;
    }

    public function setBusinesskubunAttribute($value)
    {
        $this->business_kubun = $value;
    }


    // 営業日名称
    public function getBusinessnameAttribute()
    {
        return $this->business_name;
    }

    public function setBusinessnameAttribute($value)
    {
        $this->business_name = $value;
    }


    // 休暇区分
    public function getHolidaykubunAttribute()
    {
        return $this->holiday_kubun;
    }

    public function setHolidaykubunAttribute($value)
    {
        $this->holiday_kubun = $value;
    }


    // 休暇名称
    public function getHolidaynameAttribute()
    {
        return $this->holiday_name;
    }

    public function setHolidaynameAttribute($value)
    {
        $this->holiday_name = $value;
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


    // 打刻パターン
    public function getPatternAttribute()
    {
        return $this->pattern;
    }

    public function setPatternAttribute($value)
    {
        $this->pattern = $value;
    }


    // 打刻チェック結果
    public function getCheckresultAttribute()
    {
        return $this->check_result;
    }

    public function setCheckresultAttribute($value)
    {
        $this->check_result = $value;
    }


    // 打刻回数最大チェック結果
    public function getCheckmaxtimesAttribute()
    {
        return $this->check_max_times;
    }

    public function setCheckmaxtimesAttribute($value)
    {
        $this->check_max_times = $value;
    }

    // インターバルチェック結果
    public function getCheckintervalAttribute()
    {
        return $this->check_interval;
    }

    public function setCheckintervalAttribute($value)
    {
        $this->check_interval = $value;
    }

    // 確定
    public function getFixedtimeAttribute()
    {
        return $this->fixedtime;
    }

    public function setFixedtimeAttribute($value)
    {
        $this->fixedtime = $value;
    }

    public function getSystemDateAttribute()
    {
        return $this->systemdate;
    }

    public function setSystemDateAttribute($value)
    {
        $this->systemdate = $value;
    }


    //--------------- パラメータ項目属性 -----------------------------------

    private $param_employment_status;           // 雇用形態
    private $param_user_code;                   // ユーザー
    private $param_department_code;             // 部署
    private $param_date_from;                   // 開始日付
    private $param_date_to;                     // 終了日付
    private $array_record_time;                 // 日付範囲配列
    private $massegedata;                       // メッセージ


    // 雇用形態
    public function getParamEmploymentStatusAttribute()
    {
        return $this->param_employment_status;
    }

    public function setParamEmploymentStatusAttribute($value)
    {
        $this->param_employment_status = $value;
    }

    // ユーザー
    public function getParamUsercodeAttribute()
    {
        return $this->param_user_code;
    }

    public function setParamUsercodeAttribute($value)
    {
        $this->param_user_code = $value;
    }

    // 部署
    public function getParamDepartmentcodeAttribute()
    {
        return $this->param_department_code;
    }

    public function setParamDepartmentcodeAttribute($value)
    {
        $this->param_department_code = $value;
    }


    // 開始日付
    public function getParamdatefromAttribute()
    {
        $date = date_create($this->param_date_from);
        return $date->format('Ymd');
    }

    public function setParamdatefromAttribute($value)
    {
        $this->param_date_from = $value;
    }


    // 終了日付
    public function getParamdatetoAttribute()
    {
        $date = date_create($this->param_date_to);
        return $date->format('Ymd');
    }

    public function setParamdatetoAttribute($value)
    {
        $this->param_date_to = $value;
    }

    // 日付範囲配列
    public function getArrayrecordtimeAttribute()
    {
        return $this->array_record_time;
    }

    public function setArrayrecordtimeAttribute($valuefrom, $valueto)
    {
        $this->array_record_time = array();       //初期化
        $this->array_record_time = array($valuefrom, $valueto);
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
     * 日次労働時間取得
     *
     *      指定したユーザー、日付範囲内の労働時間計算のもとデータを取得するSQL
     *
     *      INPUT：
     *          ①テーブル：departments　部署範囲内 and 削除=0
     *          ②テーブル：users　      ユーザー範囲内 and 削除=0
     *          ③テーブル：work_times　 ユーザーand日付範囲内 and 削除=0
     *
     * @return sql取得結果
     */
    public function getTempWorkingTimeDateUserJoin($targetdate){

        // 日次労働時間取得SQL作成
        $this->targetdate = $targetdate;
        // if (Config::get('const.DEBUG_LEVEL') == Config::get('const.DEBUG_LEVEL_VALUE.DEBUG')) { \DB::enableQueryLog(); }
        try{
            $subquery1 = DB::table($this->table)
                ->select(
                    $this->table.'.working_date',
                    $this->table.'.employment_status',
                    $this->table.'.department_code',
                    $this->table.'.user_code',
                    $this->table.'.seq',
                    $this->table.'.employment_status_name',
                    $this->table.'.department_name',
                    $this->table.'.user_name',
                    $this->table.'.working_timetable_no',
                    $this->table.'.working_timetable_name',
                    $this->table.'.attendance_time_1',
                    $this->table.'.attendance_time_2',
                    $this->table.'.attendance_time_3',
                    $this->table.'.attendance_time_4',
                    $this->table.'.attendance_time_5',
                    $this->table.'.attendance_time_id_1',
                    $this->table.'.attendance_time_id_2',
                    $this->table.'.attendance_time_id_3',
                    $this->table.'.attendance_time_id_4',
                    $this->table.'.attendance_time_id_5',
                    $this->table.'.attendance_editor_department_code_1',
                    $this->table.'.attendance_editor_department_code_2',
                    $this->table.'.attendance_editor_department_code_3',
                    $this->table.'.attendance_editor_department_code_4',
                    $this->table.'.attendance_editor_department_code_5',
                    $this->table.'.attendance_editor_department_name_1',
                    $this->table.'.attendance_editor_department_name_2',
                    $this->table.'.attendance_editor_department_name_3',
                    $this->table.'.attendance_editor_department_name_4',
                    $this->table.'.attendance_editor_department_name_5',
                    $this->table.'.attendance_editor_user_code_1',
                    $this->table.'.attendance_editor_user_code_2',
                    $this->table.'.attendance_editor_user_code_3',
                    $this->table.'.attendance_editor_user_code_4',
                    $this->table.'.attendance_editor_user_code_5',
                    $this->table.'.attendance_editor_user_name_1',
                    $this->table.'.attendance_editor_user_name_2',
                    $this->table.'.attendance_editor_user_name_3',
                    $this->table.'.attendance_editor_user_name_4',
                    $this->table.'.attendance_editor_user_name_5',
                    $this->table.'.leaving_time_1',
                    $this->table.'.leaving_time_2',
                    $this->table.'.leaving_time_3',
                    $this->table.'.leaving_time_4',
                    $this->table.'.leaving_time_5',
                    $this->table.'.leaving_time_id_1',
                    $this->table.'.leaving_time_id_2',
                    $this->table.'.leaving_time_id_3',
                    $this->table.'.leaving_time_id_4',
                    $this->table.'.leaving_time_id_5',
                    $this->table.'.leaving_editor_department_code_1',
                    $this->table.'.leaving_editor_department_code_2',
                    $this->table.'.leaving_editor_department_code_3',
                    $this->table.'.leaving_editor_department_code_4',
                    $this->table.'.leaving_editor_department_code_5',
                    $this->table.'.leaving_editor_department_name_1',
                    $this->table.'.leaving_editor_department_name_2',
                    $this->table.'.leaving_editor_department_name_3',
                    $this->table.'.leaving_editor_department_name_4',
                    $this->table.'.leaving_editor_department_name_5',
                    $this->table.'.leaving_editor_user_code_1',
                    $this->table.'.leaving_editor_user_code_2',
                    $this->table.'.leaving_editor_user_code_3',
                    $this->table.'.leaving_editor_user_code_4',
                    $this->table.'.leaving_editor_user_code_5',
                    $this->table.'.leaving_editor_user_name_1',
                    $this->table.'.leaving_editor_user_name_2',
                    $this->table.'.leaving_editor_user_name_3',
                    $this->table.'.leaving_editor_user_name_4',
                    $this->table.'.leaving_editor_user_name_5',
                    $this->table.'.missing_middle_time_1',
                    $this->table.'.missing_middle_time_2',
                    $this->table.'.missing_middle_time_3',
                    $this->table.'.missing_middle_time_4',
                    $this->table.'.missing_middle_time_5',
                    $this->table.'.missing_middle_time_id_1',
                    $this->table.'.missing_middle_time_id_2',
                    $this->table.'.missing_middle_time_id_3',
                    $this->table.'.missing_middle_time_id_4',
                    $this->table.'.missing_middle_time_id_5',
                    $this->table.'.missing_editor_department_code_1',
                    $this->table.'.missing_editor_department_code_2',
                    $this->table.'.missing_editor_department_code_3',
                    $this->table.'.missing_editor_department_code_4',
                    $this->table.'.missing_editor_department_code_5',
                    $this->table.'.missing_editor_department_name_1',
                    $this->table.'.missing_editor_department_name_2',
                    $this->table.'.missing_editor_department_name_3',
                    $this->table.'.missing_editor_department_name_4',
                    $this->table.'.missing_editor_department_name_5',
                    $this->table.'.missing_editor_user_code_1',
                    $this->table.'.missing_editor_user_code_2',
                    $this->table.'.missing_editor_user_code_3',
                    $this->table.'.missing_editor_user_code_4',
                    $this->table.'.missing_editor_user_code_5',
                    $this->table.'.missing_editor_user_name_1',
                    $this->table.'.missing_editor_user_name_2',
                    $this->table.'.missing_editor_user_name_3',
                    $this->table.'.missing_editor_user_name_4',
                    $this->table.'.missing_editor_user_name_5',
                    $this->table.'.missing_middle_return_time_1',
                    $this->table.'.missing_middle_return_time_2',
                    $this->table.'.missing_middle_return_time_3',
                    $this->table.'.missing_middle_return_time_4',
                    $this->table.'.missing_middle_return_time_5',
                    $this->table.'.missing_middle_return_time_id_1',
                    $this->table.'.missing_middle_return_time_id_2',
                    $this->table.'.missing_middle_return_time_id_3',
                    $this->table.'.missing_middle_return_time_id_4',
                    $this->table.'.missing_middle_return_time_id_5',
                    $this->table.'.missing_return_editor_department_code_1',
                    $this->table.'.missing_return_editor_department_code_2',
                    $this->table.'.missing_return_editor_department_code_3',
                    $this->table.'.missing_return_editor_department_code_4',
                    $this->table.'.missing_return_editor_department_code_5',
                    $this->table.'.missing_return_editor_department_name_1',
                    $this->table.'.missing_return_editor_department_name_2',
                    $this->table.'.missing_return_editor_department_name_3',
                    $this->table.'.missing_return_editor_department_name_4',
                    $this->table.'.missing_return_editor_department_name_5',
                    $this->table.'.missing_return_editor_user_code_1',
                    $this->table.'.missing_return_editor_user_code_2',
                    $this->table.'.missing_return_editor_user_code_3',
                    $this->table.'.missing_return_editor_user_code_4',
                    $this->table.'.missing_return_editor_user_code_5',
                    $this->table.'.missing_return_editor_user_name_1',
                    $this->table.'.missing_return_editor_user_name_2',
                    $this->table.'.missing_return_editor_user_name_3',
                    $this->table.'.missing_return_editor_user_name_4',
                    $this->table.'.missing_return_editor_user_name_5',
                    $this->table.'.public_going_out_time_1',
                    $this->table.'.public_going_out_time_2',
                    $this->table.'.public_going_out_time_3',
                    $this->table.'.public_going_out_time_4',
                    $this->table.'.public_going_out_time_5',
                    $this->table.'.public_going_out_time_6',
                    $this->table.'.public_going_out_time_7',
                    $this->table.'.public_going_out_time_id_1',
                    $this->table.'.public_going_out_time_id_2',
                    $this->table.'.public_going_out_time_id_3',
                    $this->table.'.public_going_out_time_id_4',
                    $this->table.'.public_going_out_time_id_5',
                    $this->table.'.public_going_out_time_id_6',
                    $this->table.'.public_going_out_time_id_7',
                    $this->table.'.public_editor_department_code_1',
                    $this->table.'.public_editor_department_code_2',
                    $this->table.'.public_editor_department_code_3',
                    $this->table.'.public_editor_department_code_4',
                    $this->table.'.public_editor_department_code_5',
                    $this->table.'.public_editor_department_code_6',
                    $this->table.'.public_editor_department_code_7',
                    $this->table.'.public_editor_department_name_1',
                    $this->table.'.public_editor_department_name_2',
                    $this->table.'.public_editor_department_name_3',
                    $this->table.'.public_editor_department_name_4',
                    $this->table.'.public_editor_department_name_5',
                    $this->table.'.public_editor_department_name_6',
                    $this->table.'.public_editor_department_name_7',
                    $this->table.'.public_editor_user_code_1',
                    $this->table.'.public_editor_user_code_2',
                    $this->table.'.public_editor_user_code_3',
                    $this->table.'.public_editor_user_code_4',
                    $this->table.'.public_editor_user_code_5',
                    $this->table.'.public_editor_user_code_6',
                    $this->table.'.public_editor_user_code_7',
                    $this->table.'.public_editor_user_name_1',
                    $this->table.'.public_editor_user_name_2',
                    $this->table.'.public_editor_user_name_3',
                    $this->table.'.public_editor_user_name_4',
                    $this->table.'.public_editor_user_name_5',
                    $this->table.'.public_editor_user_name_6',
                    $this->table.'.public_editor_user_name_7',
                    $this->table.'.public_going_out_return_time_1',
                    $this->table.'.public_going_out_return_time_2',
                    $this->table.'.public_going_out_return_time_3',
                    $this->table.'.public_going_out_return_time_4',
                    $this->table.'.public_going_out_return_time_5',
                    $this->table.'.public_going_out_return_time_6',
                    $this->table.'.public_going_out_return_time_7',
                    $this->table.'.public_going_out_return_time_id_1',
                    $this->table.'.public_going_out_return_time_id_2',
                    $this->table.'.public_going_out_return_time_id_3',
                    $this->table.'.public_going_out_return_time_id_4',
                    $this->table.'.public_going_out_return_time_id_5',
                    $this->table.'.public_going_out_return_time_id_6',
                    $this->table.'.public_going_out_return_time_id_7',
                    $this->table.'.public_return_editor_department_code_1',
                    $this->table.'.public_return_editor_department_code_2',
                    $this->table.'.public_return_editor_department_code_3',
                    $this->table.'.public_return_editor_department_code_4',
                    $this->table.'.public_return_editor_department_code_5',
                    $this->table.'.public_return_editor_department_code_6',
                    $this->table.'.public_return_editor_department_code_7',
                    $this->table.'.public_return_editor_department_name_1',
                    $this->table.'.public_return_editor_department_name_2',
                    $this->table.'.public_return_editor_department_name_3',
                    $this->table.'.public_return_editor_department_name_4',
                    $this->table.'.public_return_editor_department_name_5',
                    $this->table.'.public_return_editor_department_name_6',
                    $this->table.'.public_return_editor_department_name_7',
                    $this->table.'.public_return_editor_user_code_1',
                    $this->table.'.public_return_editor_user_code_2',
                    $this->table.'.public_return_editor_user_code_3',
                    $this->table.'.public_return_editor_user_code_4',
                    $this->table.'.public_return_editor_user_code_5',
                    $this->table.'.public_return_editor_user_code_6',
                    $this->table.'.public_return_editor_user_code_7',
                    $this->table.'.public_return_editor_user_name_1',
                    $this->table.'.public_return_editor_user_name_2',
                    $this->table.'.public_return_editor_user_name_3',
                    $this->table.'.public_return_editor_user_name_4',
                    $this->table.'.public_return_editor_user_name_5',
                    $this->table.'.public_return_editor_user_name_6',
                    $this->table.'.public_return_editor_user_name_7',
                    $this->table.'.total_working_times',
                    $this->table.'.regular_working_times',
                    $this->table.'.out_of_regular_working_times',
                    $this->table.'.overtime_hours',
                    $this->table.'.late_night_overtime_hours',
                    $this->table.'.late_night_working_hours',
                    $this->table.'.legal_working_times',
                    $this->table.'.out_of_legal_working_times',
                    $this->table.'.not_employment_working_hours',
                    $this->table.'.off_hours_working_hours',
                    $this->table.'.public_going_out_hours',
                    $this->table.'.missing_middle_hours',
                    $this->table.'.out_of_legal_working_holiday_hours',
                    $this->table.'.out_of_legal_working_holiday_night_overtime_hours',
                    $this->table.'.legal_working_holiday_hours',
                    $this->table.'.legal_working_holiday_night_overtime_hours',
                    $this->table.'.working_status',
                    $this->table.'.working_status_name',
                    $this->table.'.note',
                    $this->table.'.late',
                    $this->table.'.leave_early',
                    $this->table.'.current_calc',
                    $this->table.'.to_be_confirmed',
                    $this->table.'.weekday_kubun',
                    $this->table.'.weekday_name',
                    $this->table.'.business_kubun',
                    $this->table.'.business_name',
                    $this->table.'.holiday_kubun',
                    $this->table.'.holiday_name',
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
                    $this->table.'.pattern',
                    $this->table.'.check_result',
                    $this->table.'.check_max_times',
                    $this->table.'.check_interval',
                    $this->table.'.fixedtime');
            for ($i=1;$i<=5;$i++) {
                $case_options =
                    "  CASE IFNULL(".$this->table.'.attendance_time_positions_'.$i.',null)';
                $case_options .=
                    '  WHEN null THEN '.$this->table.'.attendance_time_positions_'.$i;
                $case_options .=
                    '  ELSE (CONCAT(X('.$this->table.'.attendance_time_positions_'.$i."), ' ' , Y(".$this->table.'.attendance_time_positions_'.$i.')))';
                $case_options .=
                    '  END as attendance_time_positions_'.$i;
                $subquery1
                    ->selectRaw($case_options);
            }
            for ($i=1;$i<=5;$i++) {
                $case_options =
                    "  CASE IFNULL(".$this->table.'.leaving_time_positions_'.$i.',null)';
                $case_options .=
                    '  WHEN null THEN '.$this->table.'.leaving_time_positions_'.$i;
                $case_options .=
                    '  ELSE (CONCAT(X('.$this->table.'.leaving_time_positions_'.$i."), ' ' , Y(".$this->table.'.leaving_time_positions_'.$i.')))';
                $case_options .=
                    '  END as leaving_time_positions_'.$i;
                $subquery1
                    ->selectRaw($case_options);
            }
            for ($i=1;$i<=5;$i++) {
                $case_options =
                    "  CASE IFNULL(".$this->table.'.missing_middle_time_positions_'.$i.',null)';
                $case_options .=
                    '  WHEN null THEN '.$this->table.'.missing_middle_time_positions_'.$i;
                $case_options .=
                    '  ELSE (CONCAT(X('.$this->table.'.missing_middle_time_positions_'.$i."), ' ' , Y(".$this->table.'.missing_middle_time_positions_'.$i.')))';
                $case_options .=
                    '  END as missing_middle_time_positions_'.$i;
                $subquery1
                    ->selectRaw($case_options);
            }
            for ($i=1;$i<=5;$i++) {
                $case_options =
                    "  CASE IFNULL(".$this->table.'.missing_middle_return_time_positions_'.$i.',null)';
                $case_options .=
                    '  WHEN null THEN '.$this->table.'.missing_middle_return_time_positions_'.$i;
                $case_options .=
                    '  ELSE (CONCAT(X('.$this->table.'.missing_middle_return_time_positions_'.$i."), ' ' , Y(".$this->table.'.missing_middle_return_time_positions_'.$i.')))';
                $case_options .=
                    '  END as missing_middle_return_time_positions_'.$i;
                $subquery1
                    ->selectRaw($case_options);
            }
            for ($i=1;$i<=7;$i++) {
                $case_options =
                    "  CASE IFNULL(".$this->table.'.public_going_out_time_positions_'.$i.',null)';
                $case_options .=
                    '  WHEN null THEN '.$this->table.'.public_going_out_time_positions_'.$i;
                $case_options .=
                    '  ELSE (CONCAT(X('.$this->table.'.public_going_out_time_positions_'.$i."), ' ' , Y(".$this->table.'.public_going_out_time_positions_'.$i.')))';
                $case_options .=
                    '  END as public_going_out_time_positions_'.$i;
                $subquery1
                    ->selectRaw($case_options);
            }
            for ($i=1;$i<=7;$i++) {
                $case_options =
                    "  CASE IFNULL(".$this->table.'.public_going_out_return_time_positions_'.$i.',null)';
                $case_options .=
                    '  WHEN null THEN '.$this->table.'.public_going_out_return_time_positions_'.$i;
                $case_options .=
                    '  ELSE (CONCAT(X('.$this->table.'.public_going_out_return_time_positions_'.$i."), ' ' , Y(".$this->table.'.public_going_out_return_time_positions_'.$i.')))';
                $case_options .=
                    '  END as public_going_out_return_time_positions_'.$i;
                $subquery1
                    ->selectRaw($case_options);
            }
     
            // 適用期間日付の取得
            $apicommon = new ApiCommonController();
            // usersの最大適用開始日付subquery
            $subquery2 = $apicommon->getUserApplyTermSubquery($targetdate);
            // departmentsの最大適用開始日付subquery
            $subquery3 = $apicommon->getDepartmentApplyTermSubquery($targetdate);

            if(!empty($this->param_date_from) && !empty($this->param_date_to)){
                $date = date_create($this->param_date_from);
                $this->param_date_from = $date->format('Ymd');
                $date = date_create($this->param_date_to);
                $this->param_date_to = $date->format('Ymd');
                $subquery1->where($this->table.'.working_date', '>=', $this->param_date_from);          // 日付範囲指定
                $subquery1->where($this->table.'.working_date', '<=', $this->param_date_to);            // 日付範囲指定
            }

            $mainquery = DB::table($this->table_users.' AS t1')
                ->selectRaw('(case when t2.working_date is not null then t2.working_date else '.$this->param_date_from.' end) as working_date');
            $mainquery->addselect('t1.employment_status')
                ->addselect('t1.department_code')
                ->addselect('t1.code as user_code')
                ->addselect('t4.code_name as employment_status_name')
                ->addselect('t3.name as department_name')
                ->addselect('t1.name as user_name')
                ->addselect('t2.working_timetable_no')
                ->addselect('t2.working_timetable_name');
            $mainquery->addselect('t1.employment_status')
                ->selectRaw('(case when t2.attendance_time_1 is not null then t2.attendance_time_1 else null end) as attendance_time_1')
                ->selectRaw('(case when t2.attendance_time_2 is not null then t2.attendance_time_2 else null end) as attendance_time_2')
                ->selectRaw('(case when t2.attendance_time_3 is not null then t2.attendance_time_3 else null end) as attendance_time_3')
                ->selectRaw('(case when t2.attendance_time_4 is not null then t2.attendance_time_4 else null end) as attendance_time_4')
                ->selectRaw('(case when t2.attendance_time_5 is not null then t2.attendance_time_5 else null end) as attendance_time_5')
                ->selectRaw('(case when t2.leaving_time_1 is not null then t2.leaving_time_1 else null end) as leaving_time_1')
                ->selectRaw('(case when t2.leaving_time_2 is not null then t2.leaving_time_2 else null end) as leaving_time_2')
                ->selectRaw('(case when t2.leaving_time_3 is not null then t2.leaving_time_3 else null end) as leaving_time_3')
                ->selectRaw('(case when t2.leaving_time_4 is not null then t2.leaving_time_4 else null end) as leaving_time_4')
                ->selectRaw('(case when t2.leaving_time_5 is not null then t2.leaving_time_5 else null end) as leaving_time_5')
                ->selectRaw('(case when t2.missing_middle_time_1 is not null then t2.missing_middle_time_1 else null end) as missing_middle_time_1')
                ->selectRaw('(case when t2.missing_middle_time_2 is not null then t2.missing_middle_time_2 else null end) as missing_middle_time_2')
                ->selectRaw('(case when t2.missing_middle_time_3 is not null then t2.missing_middle_time_3 else null end) as missing_middle_time_3')
                ->selectRaw('(case when t2.missing_middle_time_4 is not null then t2.missing_middle_time_4 else null end) as missing_middle_time_4')
                ->selectRaw('(case when t2.missing_middle_time_5 is not null then t2.missing_middle_time_5 else null end) as missing_middle_time_5')
                ->selectRaw('(case when t2.missing_middle_return_time_1 is not null then t2.missing_middle_return_time_1 else null end) as missing_middle_return_time_1')
                ->selectRaw('(case when t2.missing_middle_return_time_2 is not null then t2.missing_middle_return_time_2 else null end) as missing_middle_return_time_2')
                ->selectRaw('(case when t2.missing_middle_return_time_3 is not null then t2.missing_middle_return_time_3 else null end) as missing_middle_return_time_3')
                ->selectRaw('(case when t2.missing_middle_return_time_4 is not null then t2.missing_middle_return_time_4 else null end) as missing_middle_return_time_4')
                ->selectRaw('(case when t2.missing_middle_return_time_5 is not null then t2.missing_middle_return_time_5 else null end) as missing_middle_return_time_5')
                ->selectRaw('(case when t2.public_going_out_time_1 is not null then t2.public_going_out_time_1 else null end) as public_going_out_time_1')
                ->selectRaw('(case when t2.public_going_out_time_2 is not null then t2.public_going_out_time_2 else null end) as public_going_out_time_2')
                ->selectRaw('(case when t2.public_going_out_time_3 is not null then t2.public_going_out_time_3 else null end) as public_going_out_time_3')
                ->selectRaw('(case when t2.public_going_out_time_4 is not null then t2.public_going_out_time_4 else null end) as public_going_out_time_4')
                ->selectRaw('(case when t2.public_going_out_time_5 is not null then t2.public_going_out_time_5 else null end) as public_going_out_time_5')
                ->selectRaw('(case when t2.public_going_out_time_6 is not null then t2.public_going_out_time_6 else null end) as public_going_out_time_6')
                ->selectRaw('(case when t2.public_going_out_time_7 is not null then t2.public_going_out_time_7 else null end) as public_going_out_time_7')
                ->selectRaw('(case when t2.public_going_out_return_time_1 is not null then t2.public_going_out_return_time_1 else null end) as public_going_out_return_time_1')
                ->selectRaw('(case when t2.public_going_out_return_time_2 is not null then t2.public_going_out_return_time_2 else null end) as public_going_out_return_time_2')
                ->selectRaw('(case when t2.public_going_out_return_time_3 is not null then t2.public_going_out_return_time_3 else null end) as public_going_out_return_time_3')
                ->selectRaw('(case when t2.public_going_out_return_time_4 is not null then t2.public_going_out_return_time_4 else null end) as public_going_out_return_time_4')
                ->selectRaw('(case when t2.public_going_out_return_time_5 is not null then t2.public_going_out_return_time_5 else null end) as public_going_out_return_time_5')
                ->selectRaw('(case when t2.public_going_out_return_time_6 is not null then t2.public_going_out_return_time_6 else null end) as public_going_out_return_time_6')
                ->selectRaw('(case when t2.public_going_out_return_time_7 is not null then t2.public_going_out_return_time_7 else null end) as public_going_out_return_time_7');
            $mainquery
                ->addselect('t2.attendance_time_positions_1')
                ->addselect('t2.attendance_time_positions_2')
                ->addselect('t2.attendance_time_positions_3')
                ->addselect('t2.attendance_time_positions_4')
                ->addselect('t2.attendance_time_positions_5')
                ->addselect('t2.attendance_time_id_1')
                ->addselect('t2.attendance_time_id_2')
                ->addselect('t2.attendance_time_id_3')
                ->addselect('t2.attendance_time_id_4')
                ->addselect('t2.attendance_time_id_5')
                ->addselect('t2.attendance_editor_department_code_1')
                ->addselect('t2.attendance_editor_department_code_2')
                ->addselect('t2.attendance_editor_department_code_3')
                ->addselect('t2.attendance_editor_department_code_4')
                ->addselect('t2.attendance_editor_department_code_5')
                ->addselect('t2.attendance_editor_department_name_1')
                ->addselect('t2.attendance_editor_department_name_2')
                ->addselect('t2.attendance_editor_department_name_3')
                ->addselect('t2.attendance_editor_department_name_4')
                ->addselect('t2.attendance_editor_department_name_5')
                ->addselect('t2.attendance_editor_user_code_1')
                ->addselect('t2.attendance_editor_user_code_2')
                ->addselect('t2.attendance_editor_user_code_3')
                ->addselect('t2.attendance_editor_user_code_4')
                ->addselect('t2.attendance_editor_user_code_5')
                ->addselect('t2.attendance_editor_user_name_1')
                ->addselect('t2.attendance_editor_user_name_2')
                ->addselect('t2.attendance_editor_user_name_3')
                ->addselect('t2.attendance_editor_user_name_4')
                ->addselect('t2.attendance_editor_user_name_5')
                ->addselect('t2.leaving_time_positions_1')
                ->addselect('t2.leaving_time_positions_2')
                ->addselect('t2.leaving_time_positions_3')
                ->addselect('t2.leaving_time_positions_4')
                ->addselect('t2.leaving_time_positions_5')
                ->addselect('t2.leaving_time_id_1')
                ->addselect('t2.leaving_time_id_2')
                ->addselect('t2.leaving_time_id_3')
                ->addselect('t2.leaving_time_id_4')
                ->addselect('t2.leaving_time_id_5')
                ->addselect('t2.leaving_editor_department_code_1')
                ->addselect('t2.leaving_editor_department_code_2')
                ->addselect('t2.leaving_editor_department_code_3')
                ->addselect('t2.leaving_editor_department_code_4')
                ->addselect('t2.leaving_editor_department_code_5')
                ->addselect('t2.leaving_editor_department_name_1')
                ->addselect('t2.leaving_editor_department_name_2')
                ->addselect('t2.leaving_editor_department_name_3')
                ->addselect('t2.leaving_editor_department_name_4')
                ->addselect('t2.leaving_editor_department_name_5')
                ->addselect('t2.leaving_editor_user_code_1')
                ->addselect('t2.leaving_editor_user_code_2')
                ->addselect('t2.leaving_editor_user_code_3')
                ->addselect('t2.leaving_editor_user_code_4')
                ->addselect('t2.leaving_editor_user_code_5')
                ->addselect('t2.leaving_editor_user_name_1')
                ->addselect('t2.leaving_editor_user_name_2')
                ->addselect('t2.leaving_editor_user_name_3')
                ->addselect('t2.leaving_editor_user_name_4')
                ->addselect('t2.leaving_editor_user_name_5')
                ->addselect('t2.missing_middle_time_positions_1')
                ->addselect('t2.missing_middle_time_positions_2')
                ->addselect('t2.missing_middle_time_positions_3')
                ->addselect('t2.missing_middle_time_positions_4')
                ->addselect('t2.missing_middle_time_positions_5')
                ->addselect('t2.missing_middle_time_id_1')
                ->addselect('t2.missing_middle_time_id_2')
                ->addselect('t2.missing_middle_time_id_3')
                ->addselect('t2.missing_middle_time_id_4')
                ->addselect('t2.missing_middle_time_id_5')
                ->addselect('t2.missing_editor_department_code_1')
                ->addselect('t2.missing_editor_department_code_2')
                ->addselect('t2.missing_editor_department_code_3')
                ->addselect('t2.missing_editor_department_code_4')
                ->addselect('t2.missing_editor_department_code_5')
                ->addselect('t2.missing_editor_department_name_1')
                ->addselect('t2.missing_editor_department_name_2')
                ->addselect('t2.missing_editor_department_name_3')
                ->addselect('t2.missing_editor_department_name_4')
                ->addselect('t2.missing_editor_department_name_5')
                ->addselect('t2.missing_editor_user_code_1')
                ->addselect('t2.missing_editor_user_code_2')
                ->addselect('t2.missing_editor_user_code_3')
                ->addselect('t2.missing_editor_user_code_4')
                ->addselect('t2.missing_editor_user_code_5')
                ->addselect('t2.missing_editor_user_name_1')
                ->addselect('t2.missing_editor_user_name_2')
                ->addselect('t2.missing_editor_user_name_3')
                ->addselect('t2.missing_editor_user_name_4')
                ->addselect('t2.missing_editor_user_name_5')
                ->addselect('t2.missing_middle_return_time_positions_1')
                ->addselect('t2.missing_middle_return_time_positions_2')
                ->addselect('t2.missing_middle_return_time_positions_3')
                ->addselect('t2.missing_middle_return_time_positions_4')
                ->addselect('t2.missing_middle_return_time_positions_5')
                ->addselect('t2.missing_middle_return_time_id_1')
                ->addselect('t2.missing_middle_return_time_id_2')
                ->addselect('t2.missing_middle_return_time_id_3')
                ->addselect('t2.missing_middle_return_time_id_4')
                ->addselect('t2.missing_middle_return_time_id_5')
                ->addselect('t2.missing_return_editor_department_code_1')
                ->addselect('t2.missing_return_editor_department_code_2')
                ->addselect('t2.missing_return_editor_department_code_3')
                ->addselect('t2.missing_return_editor_department_code_4')
                ->addselect('t2.missing_return_editor_department_code_5')
                ->addselect('t2.missing_return_editor_department_name_1')
                ->addselect('t2.missing_return_editor_department_name_2')
                ->addselect('t2.missing_return_editor_department_name_3')
                ->addselect('t2.missing_return_editor_department_name_4')
                ->addselect('t2.missing_return_editor_department_name_5')
                ->addselect('t2.missing_return_editor_user_code_1')
                ->addselect('t2.missing_return_editor_user_code_2')
                ->addselect('t2.missing_return_editor_user_code_3')
                ->addselect('t2.missing_return_editor_user_code_4')
                ->addselect('t2.missing_return_editor_user_code_5')
                ->addselect('t2.missing_return_editor_user_name_1')
                ->addselect('t2.missing_return_editor_user_name_2')
                ->addselect('t2.missing_return_editor_user_name_3')
                ->addselect('t2.missing_return_editor_user_name_4')
                ->addselect('t2.missing_return_editor_user_name_5')
                ->addselect('t2.public_going_out_time_positions_1')
                ->addselect('t2.public_going_out_time_positions_2')
                ->addselect('t2.public_going_out_time_positions_3')
                ->addselect('t2.public_going_out_time_positions_4')
                ->addselect('t2.public_going_out_time_positions_5')
                ->addselect('t2.public_going_out_time_positions_6')
                ->addselect('t2.public_going_out_time_positions_7')
                ->addselect('t2.public_going_out_time_id_1')
                ->addselect('t2.public_going_out_time_id_2')
                ->addselect('t2.public_going_out_time_id_3')
                ->addselect('t2.public_going_out_time_id_4')
                ->addselect('t2.public_going_out_time_id_5')
                ->addselect('t2.public_going_out_time_id_6')
                ->addselect('t2.public_going_out_time_id_7')
                ->addselect('t2.public_editor_department_code_1')
                ->addselect('t2.public_editor_department_code_2')
                ->addselect('t2.public_editor_department_code_3')
                ->addselect('t2.public_editor_department_code_4')
                ->addselect('t2.public_editor_department_code_5')
                ->addselect('t2.public_editor_department_code_6')
                ->addselect('t2.public_editor_department_code_7')
                ->addselect('t2.public_editor_department_name_1')
                ->addselect('t2.public_editor_department_name_2')
                ->addselect('t2.public_editor_department_name_3')
                ->addselect('t2.public_editor_department_name_4')
                ->addselect('t2.public_editor_department_name_5')
                ->addselect('t2.public_editor_department_name_6')
                ->addselect('t2.public_editor_department_name_7')
                ->addselect('t2.public_editor_user_code_1')
                ->addselect('t2.public_editor_user_code_2')
                ->addselect('t2.public_editor_user_code_3')
                ->addselect('t2.public_editor_user_code_4')
                ->addselect('t2.public_editor_user_code_5')
                ->addselect('t2.public_editor_user_code_6')
                ->addselect('t2.public_editor_user_code_7')
                ->addselect('t2.public_editor_user_name_1')
                ->addselect('t2.public_editor_user_name_2')
                ->addselect('t2.public_editor_user_name_3')
                ->addselect('t2.public_editor_user_name_4')
                ->addselect('t2.public_editor_user_name_5')
                ->addselect('t2.public_editor_user_name_6')
                ->addselect('t2.public_editor_user_name_7')
                ->addselect('t2.public_going_out_return_time_positions_1')
                ->addselect('t2.public_going_out_return_time_positions_2')
                ->addselect('t2.public_going_out_return_time_positions_3')
                ->addselect('t2.public_going_out_return_time_positions_4')
                ->addselect('t2.public_going_out_return_time_positions_5')
                ->addselect('t2.public_going_out_return_time_positions_6')
                ->addselect('t2.public_going_out_return_time_positions_7')
                ->addselect('t2.public_going_out_return_time_id_1')
                ->addselect('t2.public_going_out_return_time_id_2')
                ->addselect('t2.public_going_out_return_time_id_3')
                ->addselect('t2.public_going_out_return_time_id_4')
                ->addselect('t2.public_going_out_return_time_id_5')
                ->addselect('t2.public_going_out_return_time_id_6')
                ->addselect('t2.public_going_out_return_time_id_7')
                ->addselect('t2.public_return_editor_department_code_1')
                ->addselect('t2.public_return_editor_department_code_2')
                ->addselect('t2.public_return_editor_department_code_3')
                ->addselect('t2.public_return_editor_department_code_4')
                ->addselect('t2.public_return_editor_department_code_5')
                ->addselect('t2.public_return_editor_department_code_6')
                ->addselect('t2.public_return_editor_department_code_7')
                ->addselect('t2.public_return_editor_department_name_1')
                ->addselect('t2.public_return_editor_department_name_2')
                ->addselect('t2.public_return_editor_department_name_3')
                ->addselect('t2.public_return_editor_department_name_4')
                ->addselect('t2.public_return_editor_department_name_5')
                ->addselect('t2.public_return_editor_department_name_6')
                ->addselect('t2.public_return_editor_department_name_7')
                ->addselect('t2.public_return_editor_user_code_1')
                ->addselect('t2.public_return_editor_user_code_2')
                ->addselect('t2.public_return_editor_user_code_3')
                ->addselect('t2.public_return_editor_user_code_4')
                ->addselect('t2.public_return_editor_user_code_5')
                ->addselect('t2.public_return_editor_user_code_6')
                ->addselect('t2.public_return_editor_user_code_7')
                ->addselect('t2.public_return_editor_user_name_1')
                ->addselect('t2.public_return_editor_user_name_2')
                ->addselect('t2.public_return_editor_user_name_3')
                ->addselect('t2.public_return_editor_user_name_4')
                ->addselect('t2.public_return_editor_user_name_5')
                ->addselect('t2.public_return_editor_user_name_6')
                ->addselect('t2.public_return_editor_user_name_7')
                ->addselect('t2.total_working_times')
                ->addselect('t2.regular_working_times')
                ->addselect('t2.out_of_regular_working_times')
                ->addselect('t2.overtime_hours')
                ->addselect('t2.late_night_overtime_hours')
                ->addselect('t2.late_night_working_hours')
                ->addselect('t2.legal_working_times')
                ->addselect('t2.out_of_legal_working_times')
                ->addselect('t2.not_employment_working_hours')
                ->addselect('t2.off_hours_working_hours')
                ->addselect('t2.public_going_out_hours')
                ->addselect('t2.missing_middle_hours')
                ->addselect('t2.out_of_legal_working_holiday_hours')
                ->addselect('t2.out_of_legal_working_holiday_night_overtime_hours')
                ->addselect('t2.legal_working_holiday_hours')
                ->addselect('t2.legal_working_holiday_night_overtime_hours')
                ->addselect('t2.working_status')
                ->addselect('t5.code_name as working_status_name')
                ->addselect('t2.note')
                ->addselect('t2.late')
                ->addselect('t2.leave_early')
                ->addselect('t2.current_calc')
                ->addselect('t2.to_be_confirmed')
                ->addselect('t2.weekday_kubun')
                ->addselect('t2.weekday_name')
                ->addselect('t2.business_kubun')
                ->addselect('t2.business_name')
                ->addselect('t2.holiday_kubun')
                ->addselect('t2.holiday_name')
                ->addselect('t2.closing')
                ->addselect('t2.uplimit_time')
                ->addselect('t2.statutory_uplimit_time')
                ->addselect('t2.time_unit')
                ->addselect('t2.time_rounding')
                ->addselect('t2.max_3month_total')
                ->addselect('t2.max_6month_total')
                ->addselect('t2.max_12month_total')
                ->addselect('t2.beginning_month')
                ->addselect('t2.year')
                ->addselect('t2.pattern')
                ->addselect('t2.check_result')
                ->addselect('t2.check_max_times')
                ->addselect('t2.check_interval');
            $mainquery->selectRaw(Auth::user()->id.' as created_user');
            $mainquery->selectRaw('null as updated_user');
            $mainquery->leftJoinSub($subquery1, 't2', function ($join) { 
                    $join->on('t2.user_code', '=', 't1.code');
                    $join->on('t2.department_code', '=', 't1.department_code');
                    $join->on('t2.employment_status', '=', 't1.employment_status');
                })
                ->leftJoinSub($subquery3, 't3', function ($join) { 
                    $join->on('t3.code', '=', 't1.department_code');
                })
                ->leftJoin($this->table_generalcodes.' as t4', function ($join) { 
                    $join->on('t4.code', '=', 't1.employment_status')
                    ->where('t4.identification_id', '=', Config::get('const.C001.value'))
                    ->where('t4.is_deleted', '=', 0);
                })
                ->leftJoin($this->table_generalcodes.' as t5', function ($join) { 
                    $join->on('t5.code', '=', 't2.working_status')
                    ->where('t5.identification_id', '=', Config::get('const.C012.value'))
                    ->where('t5.is_deleted', '=', 0);
                })
                ->JoinSub($subquery2, 't6', function ($join) { 
                    $join->on('t6.code', '=', 't1.code');
                    $join->on('t6.max_apply_term_from', '=', 't1.apply_term_from')
                    ->where('t1.kill_from_date', '>', $this->targetdate);
                });
                        
            if(!empty($this->param_employment_status)){
                $mainquery->where('t1.employment_status', $this->param_employment_status);      //employment_status指定
            }
            
            if(!empty($this->param_user_code)){
                $mainquery->where('t1.code', $this->param_user_code);                           //user_code指定
            }
            
            if(!empty($this->param_department_code)){
                $mainquery->where('t1.department_code', $this->param_department_code);          //department_code指定
            }
            $result = 
                $mainquery
                    ->where('t1.role', '<', Config::get('const.C017.admin_user'))
                    ->get();

            // if (Config::get('const.DEBUG_LEVEL') == Config::get('const.DEBUG_LEVEL_VALUE.DEBUG')) {
            //     \Log::debug('sql_debug_log', ['getAlertData' => \DB::getQueryLog()]);
            //     \DB::disableQueryLog();
            // }
                        
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

        return $result;
    }

    /**
     * 登録
     *
     * @return void
     */
    public function insertTempWorkingTimeDate(){
        try{
            $array_insert_items = array(
                'working_date' => $this->working_date,
                'employment_status' => $this->employment_status,
                'department_code' => $this->department_code,
                'user_code' => $this->user_code,
                'seq' => $this->seq,
                'employment_status_name' => $this->employment_status_name,
                'department_name' => $this->department_name,
                'user_name' => $this->user_name,
                'working_timetable_no' => $this->working_timetable_no,
                'working_timetable_name' => $this->working_timetable_name,
                'attendance_time_1' => $this->array_attendance_time[0],
                'attendance_time_2' => $this->array_attendance_time[1],
                'attendance_time_3' => $this->array_attendance_time[2],
                'attendance_time_4' => $this->array_attendance_time[3],
                'attendance_time_5' => $this->array_attendance_time[4],
                'attendance_time_id_1' => $this->array_attendance_time_id[0],
                'attendance_time_id_2' => $this->array_attendance_time_id[1],
                'attendance_time_id_3' => $this->array_attendance_time_id[2],
                'attendance_time_id_4' => $this->array_attendance_time_id[3],
                'attendance_time_id_5' => $this->array_attendance_time_id[4],
                'attendance_editor_department_code_1' => $this->array_attendance_editor_department_code[0],
                'attendance_editor_department_code_2' => $this->array_attendance_editor_department_code[1],
                'attendance_editor_department_code_3' => $this->array_attendance_editor_department_code[2],
                'attendance_editor_department_code_4' => $this->array_attendance_editor_department_code[3],
                'attendance_editor_department_code_5' => $this->array_attendance_editor_department_code[4],
                'attendance_editor_department_name_1' => $this->array_attendance_editor_department_name[0],
                'attendance_editor_department_name_2' => $this->array_attendance_editor_department_name[1],
                'attendance_editor_department_name_3' => $this->array_attendance_editor_department_name[2],
                'attendance_editor_department_name_4' => $this->array_attendance_editor_department_name[3],
                'attendance_editor_department_name_5' => $this->array_attendance_editor_department_name[4],
                'attendance_editor_user_code_1' => $this->array_attendance_editor_user_code[0],
                'attendance_editor_user_code_2' => $this->array_attendance_editor_user_code[1],
                'attendance_editor_user_code_3' => $this->array_attendance_editor_user_code[2],
                'attendance_editor_user_code_4' => $this->array_attendance_editor_user_code[3],
                'attendance_editor_user_code_5' => $this->array_attendance_editor_user_code[4],
                'attendance_editor_user_name_1' => $this->array_attendance_editor_user_name[0],
                'attendance_editor_user_name_2' => $this->array_attendance_editor_user_name[1],
                'attendance_editor_user_name_3' => $this->array_attendance_editor_user_name[2],
                'attendance_editor_user_name_4' => $this->array_attendance_editor_user_name[3],
                'attendance_editor_user_name_5' => $this->array_attendance_editor_user_name[4],
                'leaving_time_1' => $this->array_leaving_time[0],
                'leaving_time_2' => $this->array_leaving_time[1],
                'leaving_time_3' => $this->array_leaving_time[2],
                'leaving_time_4' => $this->array_leaving_time[3],
                'leaving_time_5' => $this->array_leaving_time[4],
                'leaving_time_id_1' => $this->array_leaving_time_id[0],
                'leaving_time_id_2' => $this->array_leaving_time_id[1],
                'leaving_time_id_3' => $this->array_leaving_time_id[2],
                'leaving_time_id_4' => $this->array_leaving_time_id[3],
                'leaving_time_id_5' => $this->array_leaving_time_id[4],
                'leaving_editor_department_code_1' => $this->array_leaving_editor_department_code[0],
                'leaving_editor_department_code_2' => $this->array_leaving_editor_department_code[1],
                'leaving_editor_department_code_3' => $this->array_leaving_editor_department_code[2],
                'leaving_editor_department_code_4' => $this->array_leaving_editor_department_code[3],
                'leaving_editor_department_code_5' => $this->array_leaving_editor_department_code[4],
                'leaving_editor_department_name_1' => $this->array_leaving_editor_department_name[0],
                'leaving_editor_department_name_2' => $this->array_leaving_editor_department_name[1],
                'leaving_editor_department_name_3' => $this->array_leaving_editor_department_name[2],
                'leaving_editor_department_name_4' => $this->array_leaving_editor_department_name[3],
                'leaving_editor_department_name_5' => $this->array_leaving_editor_department_name[4],
                'leaving_editor_user_code_1' => $this->array_leaving_editor_user_code[0],
                'leaving_editor_user_code_2' => $this->array_leaving_editor_user_code[1],
                'leaving_editor_user_code_3' => $this->array_leaving_editor_user_code[2],
                'leaving_editor_user_code_4' => $this->array_leaving_editor_user_code[3],
                'leaving_editor_user_code_5' => $this->array_leaving_editor_user_code[4],
                'leaving_editor_user_name_1' => $this->array_leaving_editor_user_name[0],
                'leaving_editor_user_name_2' => $this->array_leaving_editor_user_name[1],
                'leaving_editor_user_name_3' => $this->array_leaving_editor_user_name[2],
                'leaving_editor_user_name_4' => $this->array_leaving_editor_user_name[3],
                'leaving_editor_user_name_5' => $this->array_leaving_editor_user_name[4],
                'missing_middle_time_1' => $this->array_missing_middle_time[0],
                'missing_middle_time_2' => $this->array_missing_middle_time[1],
                'missing_middle_time_3' => $this->array_missing_middle_time[2],
                'missing_middle_time_4' => $this->array_missing_middle_time[3],
                'missing_middle_time_5' => $this->array_missing_middle_time[4],
                'missing_middle_time_id_1' => $this->array_missing_middle_time_id[0],
                'missing_middle_time_id_2' => $this->array_missing_middle_time_id[1],
                'missing_middle_time_id_3' => $this->array_missing_middle_time_id[2],
                'missing_middle_time_id_4' => $this->array_missing_middle_time_id[3],
                'missing_middle_time_id_5' => $this->array_missing_middle_time_id[4],
                'missing_editor_department_code_1' => $this->array_missing_editor_department_code[0],
                'missing_editor_department_code_2' => $this->array_missing_editor_department_code[1],
                'missing_editor_department_code_3' => $this->array_missing_editor_department_code[2],
                'missing_editor_department_code_4' => $this->array_missing_editor_department_code[3],
                'missing_editor_department_code_5' => $this->array_missing_editor_department_code[4],
                'missing_editor_department_name_1' => $this->array_missing_editor_department_name[0],
                'missing_editor_department_name_2' => $this->array_missing_editor_department_name[1],
                'missing_editor_department_name_3' => $this->array_missing_editor_department_name[2],
                'missing_editor_department_name_4' => $this->array_missing_editor_department_name[3],
                'missing_editor_department_name_5' => $this->array_missing_editor_department_name[4],
                'missing_editor_user_code_1' => $this->array_missing_editor_user_code[0],
                'missing_editor_user_code_2' => $this->array_missing_editor_user_code[1],
                'missing_editor_user_code_3' => $this->array_missing_editor_user_code[2],
                'missing_editor_user_code_4' => $this->array_missing_editor_user_code[3],
                'missing_editor_user_code_5' => $this->array_missing_editor_user_code[4],
                'missing_editor_user_name_1' => $this->array_missing_editor_user_name[0],
                'missing_editor_user_name_2' => $this->array_missing_editor_user_name[1],
                'missing_editor_user_name_3' => $this->array_missing_editor_user_name[2],
                'missing_editor_user_name_4' => $this->array_missing_editor_user_name[3],
                'missing_editor_user_name_5' => $this->array_missing_editor_user_name[4],
                'missing_middle_return_time_1' => $this->array_missing_middle_return_time[0],
                'missing_middle_return_time_2' => $this->array_missing_middle_return_time[1],
                'missing_middle_return_time_3' => $this->array_missing_middle_return_time[2],
                'missing_middle_return_time_4' => $this->array_missing_middle_return_time[3],
                'missing_middle_return_time_5' => $this->array_missing_middle_return_time[4],
                'missing_middle_return_time_id_1' => $this->array_missing_middle_return_time_id[0],
                'missing_middle_return_time_id_2' => $this->array_missing_middle_return_time_id[1],
                'missing_middle_return_time_id_3' => $this->array_missing_middle_return_time_id[2],
                'missing_middle_return_time_id_4' => $this->array_missing_middle_return_time_id[3],
                'missing_middle_return_time_id_5' => $this->array_missing_middle_return_time_id[4],
                'missing_return_editor_department_code_1' => $this->array_missing_return_editor_department_code[0],
                'missing_return_editor_department_code_2' => $this->array_missing_return_editor_department_code[1],
                'missing_return_editor_department_code_3' => $this->array_missing_return_editor_department_code[2],
                'missing_return_editor_department_code_4' => $this->array_missing_return_editor_department_code[3],
                'missing_return_editor_department_code_5' => $this->array_missing_return_editor_department_code[4],
                'missing_return_editor_department_name_1' => $this->array_missing_return_editor_department_name[0],
                'missing_return_editor_department_name_2' => $this->array_missing_return_editor_department_name[1],
                'missing_return_editor_department_name_3' => $this->array_missing_return_editor_department_name[2],
                'missing_return_editor_department_name_4' => $this->array_missing_return_editor_department_name[3],
                'missing_return_editor_department_name_5' => $this->array_missing_return_editor_department_name[4],
                'missing_return_editor_user_code_1' => $this->array_missing_return_editor_user_code[0],
                'missing_return_editor_user_code_2' => $this->array_missing_return_editor_user_code[1],
                'missing_return_editor_user_code_3' => $this->array_missing_return_editor_user_code[2],
                'missing_return_editor_user_code_4' => $this->array_missing_return_editor_user_code[3],
                'missing_return_editor_user_code_5' => $this->array_missing_return_editor_user_code[4],
                'missing_return_editor_user_name_1' => $this->array_missing_return_editor_user_name[0],
                'missing_return_editor_user_name_2' => $this->array_missing_return_editor_user_name[1],
                'missing_return_editor_user_name_3' => $this->array_missing_return_editor_user_name[2],
                'missing_return_editor_user_name_4' => $this->array_missing_return_editor_user_name[3],
                'missing_return_editor_user_name_5' => $this->array_missing_return_editor_user_name[4],
                'public_going_out_time_1' => $this->array_public_going_out_time[0],
                'public_going_out_time_2' => $this->array_public_going_out_time[1],
                'public_going_out_time_3' => $this->array_public_going_out_time[2],
                'public_going_out_time_4' => $this->array_public_going_out_time[3],
                'public_going_out_time_5' => $this->array_public_going_out_time[4],
                'public_going_out_time_6' => $this->array_public_going_out_time[5],
                'public_going_out_time_7' => $this->array_public_going_out_time[6],
                'public_going_out_time_id_1' => $this->array_public_going_out_time_id[0],
                'public_going_out_time_id_2' => $this->array_public_going_out_time_id[1],
                'public_going_out_time_id_3' => $this->array_public_going_out_time_id[2],
                'public_going_out_time_id_4' => $this->array_public_going_out_time_id[3],
                'public_going_out_time_id_5' => $this->array_public_going_out_time_id[4],
                'public_going_out_time_id_6' => $this->array_public_going_out_time_id[5],
                'public_going_out_time_id_7' => $this->array_public_going_out_time_id[6],
                'public_editor_department_code_1' => $this->array_public_editor_department_code[0],
                'public_editor_department_code_2' => $this->array_public_editor_department_code[1],
                'public_editor_department_code_3' => $this->array_public_editor_department_code[2],
                'public_editor_department_code_4' => $this->array_public_editor_department_code[3],
                'public_editor_department_code_5' => $this->array_public_editor_department_code[4],
                'public_editor_department_code_6' => $this->array_public_editor_department_code[5],
                'public_editor_department_code_7' => $this->array_public_editor_department_code[6],
                'public_editor_department_name_1' => $this->array_public_editor_department_name[0],
                'public_editor_department_name_2' => $this->array_public_editor_department_name[1],
                'public_editor_department_name_3' => $this->array_public_editor_department_name[2],
                'public_editor_department_name_4' => $this->array_public_editor_department_name[3],
                'public_editor_department_name_5' => $this->array_public_editor_department_name[4],
                'public_editor_department_name_6' => $this->array_public_editor_department_name[5],
                'public_editor_department_name_7' => $this->array_public_editor_department_name[6],
                'public_editor_user_code_1' => $this->array_public_editor_user_code[0],
                'public_editor_user_code_2' => $this->array_public_editor_user_code[1],
                'public_editor_user_code_3' => $this->array_public_editor_user_code[2],
                'public_editor_user_code_4' => $this->array_public_editor_user_code[3],
                'public_editor_user_code_5' => $this->array_public_editor_user_code[4],
                'public_editor_user_code_6' => $this->array_public_editor_user_code[5],
                'public_editor_user_code_7' => $this->array_public_editor_user_code[6],
                'public_editor_user_name_1' => $this->array_public_editor_user_name[0],
                'public_editor_user_name_2' => $this->array_public_editor_user_name[1],
                'public_editor_user_name_3' => $this->array_public_editor_user_name[2],
                'public_editor_user_name_4' => $this->array_public_editor_user_name[3],
                'public_editor_user_name_5' => $this->array_public_editor_user_name[4],
                'public_editor_user_name_6' => $this->array_public_editor_user_name[5],
                'public_editor_user_name_7' => $this->array_public_editor_user_name[6],
                'public_going_out_return_time_1' => $this->array_public_going_out_return_time[0],
                'public_going_out_return_time_2' => $this->array_public_going_out_return_time[1],
                'public_going_out_return_time_3' => $this->array_public_going_out_return_time[2],
                'public_going_out_return_time_4' => $this->array_public_going_out_return_time[3],
                'public_going_out_return_time_5' => $this->array_public_going_out_return_time[4],
                'public_going_out_return_time_6' => $this->array_public_going_out_return_time[5],
                'public_going_out_return_time_7' => $this->array_public_going_out_return_time[6],
                'public_going_out_return_time_id_1' => $this->array_public_going_out_return_time_id[0],
                'public_going_out_return_time_id_2' => $this->array_public_going_out_return_time_id[1],
                'public_going_out_return_time_id_3' => $this->array_public_going_out_return_time_id[2],
                'public_going_out_return_time_id_4' => $this->array_public_going_out_return_time_id[3],
                'public_going_out_return_time_id_5' => $this->array_public_going_out_return_time_id[4],
                'public_going_out_return_time_id_6' => $this->array_public_going_out_return_time_id[5],
                'public_going_out_return_time_id_7' => $this->array_public_going_out_return_time_id[6],
                'public_return_editor_department_code_1' => $this->array_public_return_editor_department_code[0],
                'public_return_editor_department_code_2' => $this->array_public_return_editor_department_code[1],
                'public_return_editor_department_code_3' => $this->array_public_return_editor_department_code[2],
                'public_return_editor_department_code_4' => $this->array_public_return_editor_department_code[3],
                'public_return_editor_department_code_5' => $this->array_public_return_editor_department_code[4],
                'public_return_editor_department_code_6' => $this->array_public_return_editor_department_code[5],
                'public_return_editor_department_code_7' => $this->array_public_return_editor_department_code[6],
                'public_return_editor_department_name_1' => $this->array_public_return_editor_department_name[0],
                'public_return_editor_department_name_2' => $this->array_public_return_editor_department_name[1],
                'public_return_editor_department_name_3' => $this->array_public_return_editor_department_name[2],
                'public_return_editor_department_name_4' => $this->array_public_return_editor_department_name[3],
                'public_return_editor_department_name_5' => $this->array_public_return_editor_department_name[4],
                'public_return_editor_department_name_6' => $this->array_public_return_editor_department_name[5],
                'public_return_editor_department_name_7' => $this->array_public_return_editor_department_name[6],
                'public_return_editor_user_code_1' => $this->array_public_return_editor_user_code[0],
                'public_return_editor_user_code_2' => $this->array_public_return_editor_user_code[1],
                'public_return_editor_user_code_3' => $this->array_public_return_editor_user_code[2],
                'public_return_editor_user_code_4' => $this->array_public_return_editor_user_code[3],
                'public_return_editor_user_code_5' => $this->array_public_return_editor_user_code[4],
                'public_return_editor_user_code_6' => $this->array_public_return_editor_user_code[5],
                'public_return_editor_user_code_7' => $this->array_public_return_editor_user_code[6],
                'public_return_editor_user_name_1' => $this->array_public_return_editor_user_name[0],
                'public_return_editor_user_name_2' => $this->array_public_return_editor_user_name[1],
                'public_return_editor_user_name_3' => $this->array_public_return_editor_user_name[2],
                'public_return_editor_user_name_4' => $this->array_public_return_editor_user_name[3],
                'public_return_editor_user_name_5' => $this->array_public_return_editor_user_name[4],
                'public_return_editor_user_name_6' => $this->array_public_return_editor_user_name[5],
                'public_return_editor_user_name_7' => $this->array_public_return_editor_user_name[6],
                'total_working_times' => $this->total_working_times,
                'regular_working_times' => $this->regular_working_times,
                'out_of_regular_working_times' => $this->out_of_regular_working_times,
                'overtime_hours' => $this->overtime_hours,
                'late_night_overtime_hours' => $this->late_night_overtime_hours,
                'late_night_working_hours' => $this->late_night_working_hours,
                'legal_working_times' => $this->legal_working_times,
                'out_of_legal_working_times' => $this->out_of_legal_working_times,
                'not_employment_working_hours' => $this->not_employment_working_hours,
                'off_hours_working_hours' => $this->off_hours_working_hours,
                'public_going_out_hours' => $this->public_going_out_hours,
                'missing_middle_hours' => $this->missing_middle_hours,
                'out_of_legal_working_holiday_hours' => $this->out_of_legal_working_holiday_hours,
                'out_of_legal_working_holiday_night_overtime_hours' => $this->out_of_legal_working_holiday_night_overtime_hours,
                'legal_working_holiday_hours' => $this->legal_working_holiday_hours,
                'legal_working_holiday_night_overtime_hours' => $this->legal_working_holiday_night_overtime_hours,
                'working_status' => $this->working_status,
                'working_status_name' => $this->working_status_name,
                'note' => $this->note,
                'late' => $this->late,
                'leave_early' => $this->leave_early,
                'current_calc' => $this->current_calc,
                'to_be_confirmed' => $this->to_be_confirmed,
                'weekday_kubun' => $this->weekday_kubun,
                'weekday_name' => $this->weekday_name,
                'business_kubun' => $this->business_kubun,
                'business_name' => $this->business_name,
                'holiday_kubun' => $this->holiday_kubun,
                'holiday_name' => $this->holiday_name,
                'closing' => $this->closing,
                'uplimit_time' => $this->uplimit_time,
                'statutory_uplimit_time' => $this->statutory_uplimit_time,
                'time_unit' => $this->time_unit,
                'time_rounding' => $this->time_rounding,
                'max_3month_total' => $this->max_3month_total,
                'max_6month_total' => $this->max_6month_total,
                'max_12month_total' => $this->max_12month_total,
                'beginning_month' => $this->beginning_month,
                'year' => $this->year,
                'pattern' => $this->pattern,
                'check_result' => $this->check_result,
                'check_max_times' => $this->check_max_times,
                'check_interval' => $this->check_interval,
                'fixedtime' => $this->fixedtime,
                'created_at'=>$this->systemdate
            );
            $array_length = count($this->array_attendance_time_positions);
            if ($array_length > 5) {$array_length = 5;}
            for ($i=0;$i<count($this->array_attendance_time_positions);$i++) {
                if (isset($this->array_attendance_time_positions[$i])) {
                    $array_insert_items = array_merge($array_insert_items,
                        array('attendance_time_positions_'.($i+1) => DB::raw("(GeomFromText('POINT(".$this->array_attendance_time_positions[$i].")'))"))
                    );
                } else {
                    $array_insert_items = array_merge($array_insert_items,
                        array('attendance_time_positions_'.($i+1) => null)
                    );
                }
            }
            $array_length = count($this->array_leaving_time_positions);
            if ($array_length > 5) {$array_length = 5;}
            for ($i=0;$i<count($this->array_leaving_time_positions);$i++) {
                if (isset($this->array_leaving_time_positions[$i])) {
                    $array_insert_items = array_merge($array_insert_items,
                        array('leaving_time_positions_'.($i+1) => DB::raw("(GeomFromText('POINT(".$this->array_leaving_time_positions[$i].")'))"))
                    );
                } else {
                    $array_insert_items = array_merge($array_insert_items,
                        array('leaving_time_positions_'.($i+1) => null)
                    );
                }
            }
            $array_length = count($this->array_missing_middle_time_positions);
            if ($array_length > 5) {$array_length = 5;}
            for ($i=0;$i<count($this->array_missing_middle_time_positions);$i++) {
                if (isset($this->array_missing_middle_time_positions[$i])) {
                    $array_insert_items = array_merge($array_insert_items,
                        array('missing_middle_time_positions_'.($i+1) => DB::raw("(GeomFromText('POINT(".$this->array_missing_middle_time_positions[$i].")'))"))
                    );
                } else {
                    $array_insert_items = array_merge($array_insert_items,
                        array('missing_middle_time_positions_'.($i+1) => null)
                    );
                }
            }
            $array_length = count($this->array_missing_middle_return_time_positions);
            if ($array_length > 5) {$array_length = 5;}
            for ($i=0;$i<count($this->array_missing_middle_return_time_positions);$i++) {
                if (isset($this->array_missing_middle_return_time_positions[$i])) {
                    $array_insert_items = array_merge($array_insert_items,
                        array('missing_middle_return_time_positions_'.($i+1) => DB::raw("(GeomFromText('POINT(".$this->array_missing_middle_return_time_positions[$i].")'))"))
                    );
                } else {
                    $array_insert_items = array_merge($array_insert_items,
                        array('missing_middle_return_time_positions_'.($i+1) => null)
                    );
                }
            }
            // 
            $array_length = count($this->array_public_going_out_time_positions);
            if ($array_length > 7) {$array_length = 7;}
            for ($i=0;$i<$array_length;$i++) {
                if (isset($this->array_public_going_out_time_positions[$i])) {
                    $array_insert_items = array_merge($array_insert_items,
                        array('public_going_out_time_positions_'.($i+1) => DB::raw("(GeomFromText('POINT(".$this->array_public_going_out_time_positions[$i].")'))"))
                    );
                } else {
                    $array_insert_items = array_merge($array_insert_items,
                        array('public_going_out_time_positions_'.($i+1) => null)
                    );
                }
            }
            $array_length = count($this->array_public_going_out_return_time_positions);
            if ($array_length > 7) {$array_length = 7;}
            for ($i=0;$i<count($this->array_public_going_out_return_time_positions);$i++) {
                if (isset($this->array_public_going_out_return_time_positions[$i])) {
                    $array_insert_items = array_merge($array_insert_items,
                        array('public_going_out_return_time_positions_'.($i+1) => DB::raw("(GeomFromText('POINT(".$this->array_public_going_out_return_time_positions[$i].")'))"))
                    );
                } else {
                    $array_insert_items = array_merge($array_insert_items,
                        array('public_going_out_return_time_positions_'.($i+1) => null)
                    );
                }
            }
            DB::table($this->table)->insert($array_insert_items);
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_insert_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_insert_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 削除
     *
     * @return void
     */
    public function delTempWorkingTimeDate(){
        try{
            $mainquery = DB::table($this->table)->truncate();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_delete_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_delete_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

    }

}
