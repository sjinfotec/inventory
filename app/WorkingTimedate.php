<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\TempWorkingTimeDate;
use App\Http\Controllers\ApiCommonController;
use App\Setting;


/**
 * テーブル：日次タイムレコード（working_time_date）のモデル
 *      アクセサ定義
 * @author      o.shindo
 * @version     1.00    20190629
*/
class WorkingTimedate extends Model
{
    protected $table = 'working_time_dates';
    protected $table_temp_working_time_dates = 'temp_working_time_dates';
    protected $table_work_times = 'work_times';
    protected $table_users = 'users';
    protected $table_departments = 'departments';
    protected $table_user_holiday_kubuns = 'user_holiday_kubuns';
    protected $table_calendars = 'calendars';
    protected $table_generalcodes = 'generalcodes';
    protected $guarded = array('id');

    //--------------- 項目属性 -----------------------------------

    private $working_date;                  // 日付
    private $employment_status;             // 雇用形態
    private $department_code;               // 部署ID
    private $user_code;                     // ユーザー
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
    private $array_public_going_out_time = [null,null,null,null,null];                      // 公用外出時刻
    private $array_public_going_out_time_positions = [null,null,null,null,null];            // 公用外出位置情報
    private $array_public_going_out_time_id = [null,null,null,null,null];                   // 公用外出打刻時刻テーブルID
    private $array_public_editor_department_code = [null,null,null,null,null];              // 公用外出編集部署コード
    private $array_public_editor_user_code = [null,null,null,null,null];                    // 公用外出編集ユーザーコード
    private $array_public_editor_department_name = [null,null,null,null,null];              // 公用外出編集部署名
    private $array_public_editor_user_name = [null,null,null,null,null];                    // 公用外出編集ユーザー名
    private $array_public_going_out_return_time = [null,null,null,null,null];               // 公用外出戻り時刻
    private $array_public_going_out_return_time_positions = [null,null,null,null,null];     // 公用外出戻り位置情報
    private $array_public_going_out_return_time_id = [null,null,null,null,null];            // 公用外出戻り打刻時刻テーブルID
    private $array_public_return_editor_department_code = [null,null,null,null,null];       // 公用外出戻り編集部署コード
    private $array_public_return_editor_user_code = [null,null,null,null,null];             // 公用外出戻り編集ユーザーコード
    private $array_public_return_editor_department_name = [null,null,null,null,null];       // 公用外出戻り編集部署名
    private $array_public_return_editor_user_name = [null,null,null,null,null];             // 公用外出戻り編集ユーザー名
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
    private $out_of_legal_working_holiday_hours;    // 法定外休日労働時間
    private $legal_working_holiday_hours;           // 法定休日労働時間
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
    private $fixedtime;                     // 確定
    private $created_user;                  // 作成ユーザー
    private $updated_user;                  // 修正ユーザー
    private $is_deleted;                    // 削除フラグ
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
        $this->array_attendance_time_id[$index] = $value;
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

    // 出勤編集ユーザー名
    public function getAttendanceeditorusernameAttribute($index)
    {
        return $this->array_attendance_editor_user_name[$index];
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
        $this->array_leaving_time_id[$index] = $value;
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

    // 退勤編集ユーザー名
    public function getLeavingeditorusernameAttribute($index)
    {
        return $this->array_leaving_editor_user_name[$index];
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
        $this->array_missing_middle_time_id[$index] = $value;
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

    // 私用外出編集ユーザー名
    public function getMissingeditorusernameAttribute($index)
    {
        return $this->array_missing_editor_user_name[$index];
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
        $this->array_missing_middle_return_time_id[$index] = $value;
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


    // 私用外出戻り編集ユーザー名
    public function getMissingreturneditorusernameAttribute($index)
    {
        return $this->array_missing_return_editor_user_name[$index];
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
        $this->array_public_going_out_time_id[$index] = $value;
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

    // 公用外出編集ユーザー名
    public function getPubliceditorusernameAttribute($index)
    {
        return $this->array_public_editor_user_name[$index];
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
        $this->array_public_going_out_return_time_id[$index] = $value;
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

    // 公用外出戻り編集ユーザー名
    public function getPublicreturneditorusernameAttribute($index)
    {
        return $this->array_public_return_editor_user_name[$index];
    }

    // 合計勤務時間
    public function getTotalworkingtimesAttribute()
    {
        return $this->total_working_times;
    }

    public function setTotalworkingtimesAttribute($value)
    {
        $this->total_working_times = $value;
    }


    // 所定労働時間
    public function getRegularworkingtimesAttribute()
    {
        return $this->regular_working_times;
    }

    public function setRegularworkingtimesAttribute($value)
    {
        $this->regular_working_times = $value;
    }


    // 所定外労働時間
    public function getOutofregularworkingtimesAttribute()
    {
        return $this->out_of_regular_working_times;
    }

    public function setOutofregularworkingtimesAttribute($value)
    {
        $this->out_of_regular_working_times = $value;
    }


    // 残業時間
    public function getOvertimehoursAttribute()
    {
        return $this->overtime_hours;
    }

    public function setOvertimehoursAttribute($value)
    {
        $this->overtime_hours = $value;
    }


    // 深夜残業時間
    public function getLatenightovertimehoursAttribute()
    {
        return $this->late_night_overtime_hours;
    }

    public function setLatenightovertimehoursAttribute($value)
    {
        $this->late_night_overtime_hours = $value;
    }


    // 深夜労働時間
    public function getLatenightworkinghoursAttribute()
    {
        return $this->late_night_working_hours;
    }

    public function setLatenightworkinghoursAttribute($value)
    {
        $this->late_night_working_hours = $value;
    }


    // 法定労働時間
    public function getLegalworkingtimesAttribute()
    {
        return $this->legal_working_times;
    }

    public function setLegalworkingtimesAttribute($value)
    {
        $this->legal_working_times = $value;
    }


    // 法定外労働時間
    public function getOutoflegalworkingtimesAttribute()
    {
        return $this->out_of_legal_working_times;
    }

    public function setOutoflegalworkingtimesAttribute($value)
    {
        $this->out_of_legal_working_times = $value;
    }


    // 未就労労働時間
    public function getNotemploymentworkinghoursAttribute()
    {
        return $this->not_employment_working_hours;
    }

    public function setNotemploymentworkinghoursAttribute($value)
    {
        $this->not_employment_working_hours = $value;
    }


    // 時間外労働時間
    public function getOffhoursworkinghoursAttribute()
    {
        return $this->off_hours_working_hours;
    }

    public function setOffhoursworkinghoursAttribute($value)
    {
        $this->off_hours_working_hours = $value;
    }

    // 私用外出時間
    public function getMissingmiddlehoursAttribute()
    {
        return $this->missing_middle_hours;
    }

    public function setMissingmiddlehoursAttribute($value)
    {
        $this->missing_middle_hours = $value;
    }

    // 公用外出時間
    public function getPublicgoingouthoursAttribute()
    {
        return $this->public_going_out_hours;
    }

    public function setPublicgoingouthoursAttribute($value)
    {
        $this->public_going_out_hours = $value;
    }

    // 法定外休日労働時間
    public function getOutoflegalworkingholidayhoursAttribute()
    {
        return $this->out_of_legal_working_holiday_hours;
    }

    public function setOutoflegalworkingholidayhoursAttribute($value)
    {
        $this->out_of_legal_working_holiday_hours = $value;
    }

    // 法定休日労働時間
    public function getLegalworkingholidayhoursAttribute()
    {
        return $this->legal_working_holiday_hours;
    }

    public function setLegalworkingholidayhoursAttribute($value)
    {
        $this->legal_working_holiday_hours = $value;
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
    // 月次アラート用に開始終了日付を１２か月分定義
    private $array_param_date_from = array();   // 開始日付
    private $array_param_date_to = array();     // 終了日付

    private $array_record_time;                 // 日付範囲配列
    private $massegedata = array();             // メッセージ


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

    //  月次アラート用開始日付
    public function getArrayParamdatefromAttribute()
    {
        return $this->array_param_date_from;
    }

    // 入力日付と検索区分
    public function setArrayParamdatefromAttribute($value, $search_kbn)
    {
        $this->massegedata = array();
        $this->array_param_date_from = array();
        $dt = new Carbon($value.'01');
        $to_date_closing = '';
        for ($i=0;$i<12;$i++) {
            $this->array_param_date_from[$i] = null;
        }
        $set_from_date_flg = false;
        if ($search_kbn == Config::get('const.C022.monthly_alert_begining_month_closing') ||
            $search_kbn == Config::get('const.C022.monthly_alert_begining_month_first') ||
            $search_kbn == Config::get('const.C022.monthly_alert_first_month_closing')) {
            // 期首月と〆日取得
            $dt_first = null;
            $setting_model = new Setting();
            $setting_model->setParamYearAttribute(date_format($dt, 'Y'));
            if ($search_kbn == Config::get('const.C022.monthly_alert_begining_month_closing') ||
                $search_kbn == Config::get('const.C022.monthly_alert_begining_month_first')) {
                $settings = $setting_model->getSettingDatasYearOrderBy(1);
            } elseif ($search_kbn == Config::get('const.C022.monthly_alert_first_month_closing')) {
                $settings = $setting_model->getSettingDatasYearOrderBy(2);
            }
            $set_beginning_or_first_ymd_flg = false;
            $set_from_date_flg = false;
            $set_index = 0;
            foreach($settings as $setting) {
                if ($search_kbn == Config::get('const.C022.monthly_alert_begining_month_closing') ||
                    $search_kbn == Config::get('const.C022.monthly_alert_begining_month_first')) {
                    if (!isset($setting->beginning_month)) {
                        $this->massegedata[] = array(Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.not_setting_beginning_month'));
                        break;
                    }
                }
                if ($search_kbn == Config::get('const.C022.monthly_alert_begining_month_closing') ||
                    $search_kbn == Config::get('const.C022.monthly_alert_first_month_closing')) {
                    if (!isset($setting->closing)) {
                        $this->massegedata[] = array(Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.not_setting_closing'));
                        break;
                    }
                }
                if (!isset($setting->fiscal_month)) {
                    $this->massegedata[] = array(Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.not_setting_fiscal_month'));
                    break;
                }
                $ymd_year = 0;
                if ($search_kbn == Config::get('const.C022.monthly_alert_begining_month_closing') ||
                    $search_kbn == Config::get('const.C022.monthly_alert_begining_month_first')) {
                    $ymd_year = $setting->year;
                } elseif ($search_kbn == Config::get('const.C022.monthly_alert_first_month_closing')) {
                    $ymd_year = $setting->fiscal_year;
                }
                // 開始年月の設定
                $settings_ymd = new Carbon($ymd_year.str_pad($setting->fiscal_month, 2, 0, STR_PAD_LEFT).'01');
                $settings_ym = date_format($settings_ymd,'Ym');
                if (!$set_beginning_or_first_ymd_flg) {
                    $beginning_ymd = new Carbon($ymd_year.str_pad($setting->beginning_month, 2, 0, STR_PAD_LEFT).'01');
                    if ($search_kbn == Config::get('const.C022.monthly_alert_begining_month_closing') ||
                        $search_kbn == Config::get('const.C022.monthly_alert_begining_month_first')) {
                        $first_ymd = new Carbon($ymd_year.str_pad(1, 2, 0, STR_PAD_LEFT).'01');
                    } elseif ($search_kbn == Config::get('const.C022.monthly_alert_first_month_closing')) {
                        $first_ymd = new Carbon($setting->fiscal_year.str_pad(1, 2, 0, STR_PAD_LEFT).'01');
                    }
                    $beginning_ym = date_format($beginning_ymd,'Ym');
                    $first_ym = date_format($first_ymd,'Ym');
                    $set_beginning_or_first_ymd_flg = true;
                    }
                if ($search_kbn == Config::get('const.C022.monthly_alert_begining_month_closing')) {
                    if ($settings_ym == $beginning_ym) {
                        $begining_closing_ymd = new Carbon($ymd_year.str_pad($setting->beginning_month, 2, 0, STR_PAD_LEFT).str_pad($setting->closing, 2, 0, STR_PAD_LEFT));
                        $closing_from_ymd = new Carbon($begining_closing_ymd);
                        $date_from = $closing_from_ymd->subMonthNoOverflow()->addDay();
                        $date_from_ym = date_format(new Carbon($date_from),'Ym');
                        $closing_to_ymd = new Carbon($date_from_ym.'01');
                        $date_to = $closing_to_ymd->addYear();
                        $date_to_ym = date_format(new Carbon($date_to),'Ym');
                        $set_from_date_flg = true;
                        $set_index = 0;
                    }
                }
                if ($search_kbn == Config::get('const.C022.monthly_alert_begining_month_first')) {
                    if ($settings_ym == $beginning_ym) {
                        $begining_first_ymd = new Carbon($ymd_year.str_pad($setting->beginning_month, 2, 0, STR_PAD_LEFT).'01');
                        $date_from_ym = date_format(new Carbon($begining_first_ymd),'Ym');
                        $closing_to_ymd = new Carbon($date_from_ym.'01');
                        $date_to = $closing_to_ymd->addYear();
                        $date_to_ym = date_format(new Carbon($date_to),'Ym');
                        $set_from_date_flg = true;
                        $set_index = 0;
                    }
                }
                if ($search_kbn == Config::get('const.C022.monthly_alert_first_month_closing')) {
                    if ($settings_ym == $first_ym) {
                        $first_closing_ymd = new Carbon($ymd_year.str_pad(1, 2, 0, STR_PAD_LEFT).str_pad($setting->closing, 2, 0, STR_PAD_LEFT));
                        $closing_from_ymd = new Carbon($first_closing_ymd);
                        $date_from = $closing_from_ymd->subMonthNoOverflow()->addDay();
                        $date_from_ym = date_format(new Carbon($date_from),'Ym');
                        $closing_to_ymd = new Carbon($date_from_ym.'01');
                        $date_to = $closing_to_ymd->addYear();
                        $date_to_ym = date_format(new Carbon($date_to),'Ym');
                        $set_from_date_flg = true;
                        $set_index = 0;
                    }
                }
                // 開始終了日付設定された場合
                if ($set_from_date_flg) {
                    if ($search_kbn == Config::get('const.C022.monthly_alert_begining_month_closing')) {
                        $begining_closing_ymd = new Carbon($ymd_year.str_pad($setting->fiscal_month, 2, 0, STR_PAD_LEFT).str_pad($setting->closing, 2, 0, STR_PAD_LEFT));
                        $set_closing_ymd = new Carbon($begining_closing_ymd);
                        $this->array_param_date_from[$set_index] = date_format($set_closing_ymd->subMonthNoOverflow()->addDay(),'Ymd');
                    }
                    if ($search_kbn == Config::get('const.C022.monthly_alert_begining_month_first')) {
                        $begining_first_ymd = new Carbon($ymd_year.str_pad($setting->fiscal_month, 2, 0, STR_PAD_LEFT).'01');
                        $this->array_param_date_from[$set_index] = date_format($begining_first_ymd,'Ymd');
                    }
                    if ($search_kbn == Config::get('const.C022.monthly_alert_first_month_closing')) {
                        $first_closing_ymd = new Carbon($ymd_year.str_pad($setting->fiscal_month, 2, 0, STR_PAD_LEFT).str_pad($setting->closing, 2, 0, STR_PAD_LEFT));
                        $set_closing_ymd = new Carbon($first_closing_ymd);
                        $this->array_param_date_from[$set_index] = date_format($set_closing_ymd->subMonthNoOverflow()->addDay(),'Ymd');
                    }
                    $set_index++;
                    // 終了月を超えたら終わり
                    if (date_format($settings_ymd, 'Ym') >= $date_to_ym) {
                        // 終了日付設定のため、締日を保存
                        $to_date_closing = $setting->closing;
                        break;
                    }
                }
            }
            if (!$set_from_date_flg) {
                $this->massegedata[] = array(Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.not_setting_closing'));
            }
        } else {
            $set_from_date_flg = true;
            $dt_first = new Carbon(date_format($dt, 'Y').'0101');
            $date_to_ymd = new Carbon(date_format($dt, 'Y').'0101');
            // $dt_firstから$valueまで各月１日を設定
            $date_to_ym = date_format($date_to_ymd->addYear()->subMonthNoOverflow(), 'Ym');
            $set_date = new Carbon($dt_first);
            $i = 0;
            while(true) {
                $this->array_param_date_from[$i] = date_format(new Carbon($set_date),'Ymd');
                if (date_format($set_date, 'Ym') >= $date_to_ym) {
                    break;
                }
                $set_date = new Carbon($dt_first->addMonthNoOverflow(1));
                $dt_first = $set_date;
                $i++;
            }
        }
        if ($set_from_date_flg) {
            $this->setArrayParamdatetoAttribute($this->array_param_date_from, $to_date_closing, $search_kbn);
        }
    }

    //  月次アラート用終了日付
    public function getArrayParamdatetoAttribute()
    {
        return $this->array_param_date_to;
    }

    //  月次アラート用終了日付（月次アラート用開始日付から設定するので$valueは）
    public function setArrayParamdatetoAttribute($value, $closing, $search_kbn)
    {
        for ($i=0;$i<12;$i++) {
            $this->array_param_date_to[$i] = null;
        }
        if ($search_kbn == Config::get('const.C022.monthly_alert_begining_month_closing') ||
            $search_kbn == Config::get('const.C022.monthly_alert_first_month_closing')) {
            for ($i=1;$i<count($value);$i++) {
                if (isset($value[$i])) {
                    $dt = new Carbon($value[$i]);
                    $dt_last = new Carbon($value[$i]);
                    $this->array_param_date_to[$i-1] = date_format($dt->subDay(),'Ymd');
                }
            }
            $dt = new Carbon(date_format($dt_last->addMonthNoOverflow(1),'Ym').str_pad($closing, 2, 0, STR_PAD_LEFT));
            $this->array_param_date_to[$i-1] = date_format($dt->subDay(),'Ymd');
        } else {
            for ($i=0;$i<=count($value)-1;$i++) {
                if (isset($value[$i])) {
                    $dt = new Carbon($value[$i]);
                    $dt_last = $dt->addMonthNoOverflow(1);
                    $this->array_param_date_to[$i] = date_format($dt_last->subDay(),'Ymd');
                }
            }
        }
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
     * 日次労働時間取得事前チェック
     *
     *      指定したユーザー、部署、日付範囲内の労働時間計算のもとデータを取得するSQL
     *
     *      INPUT：
     *          ①テーブル：departments　指定部署内 and 削除=0
     *          ②テーブル：users　      指定ユーザー内 and 削除=0
     *          ③テーブル：work_times　 ユーザーand日付範囲内 and 削除=0
     *          ④①と②と③の結合          ①.ユーザー = ②.ユーザー and ②.ユーザー = ③.ユーザー
     *
     *      チェック方法：
     *          ①日付範囲指定必須チェック
     *          ②user_code範囲指定プロパティを事前設定（未設定有効）
     *          ③日付範囲指定プロパティを事前設定（未設定無効）
     *          ④メソッド：calcWorkingTimeDateを実行
     *s
     * @return sql取得結果
     */
    public function chkWorkingTimeData() {

        $this->massegedata = array();
        $result = true;

        // 日付範囲指定必須チェック
        if(isset($this->param_date_from) && isset($this->param_date_to)) {
            // 日付範囲指定比較チェック
            $chkDateFrom = $this->getParamDatefromAttribute();
            $chkDateTo = $this->getParamDatetoAttribute();
            if($chkDateFrom <= $chkDateTo){
                $this->setArrayrecordtimeAttribute($chkDateFrom, $chkDateTo);
            } else {
                $this->massegedata[] = array(Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.not_between_workindate'));
                $result = false;
            }
        } else {
            $this->massegedata[] = array(Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.not_input_workindatefromto'));
            $result = false;
        }

        return $result;

    }


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
    public function getWorkingTimeDate(){


        // 日次労働時間取得SQL作成
        try{
            $mainquery = DB::table($this->table)
                ->select(
                    $this->table.'.working_date',
                    $this->table.'.employment_status',
                    $this->table.'.department_code',
                    $this->table.'.user_code',
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
                    $this->table.'.public_going_out_time_id_1',
                    $this->table.'.public_going_out_time_id_2',
                    $this->table.'.public_going_out_time_id_3',
                    $this->table.'.public_going_out_time_id_4',
                    $this->table.'.public_going_out_time_id_5',
                    $this->table.'.public_editor_department_code_1',
                    $this->table.'.public_editor_department_code_2',
                    $this->table.'.public_editor_department_code_3',
                    $this->table.'.public_editor_department_code_4',
                    $this->table.'.public_editor_department_code_5',
                    $this->table.'.public_editor_department_name_1',
                    $this->table.'.public_editor_department_name_2',
                    $this->table.'.public_editor_department_name_3',
                    $this->table.'.public_editor_department_name_4',
                    $this->table.'.public_editor_department_name_5',
                    $this->table.'.public_editor_user_code_1',
                    $this->table.'.public_editor_user_code_2',
                    $this->table.'.public_editor_user_code_3',
                    $this->table.'.public_editor_user_code_4',
                    $this->table.'.public_editor_user_code_5',
                    $this->table.'.public_editor_user_name_1',
                    $this->table.'.public_editor_user_name_2',
                    $this->table.'.public_editor_user_name_3',
                    $this->table.'.public_editor_user_name_4',
                    $this->table.'.public_editor_user_name_5',
                    $this->table.'.public_going_out_return_time_1',
                    $this->table.'.public_going_out_return_time_2',
                    $this->table.'.public_going_out_return_time_3',
                    $this->table.'.public_going_out_return_time_4',
                    $this->table.'.public_going_out_return_time_5',
                    $this->table.'.public_going_out_return_time_id_1',
                    $this->table.'.public_going_out_return_time_id_2',
                    $this->table.'.public_going_out_return_time_id_3',
                    $this->table.'.public_going_out_return_time_id_4',
                    $this->table.'.public_going_out_return_time_id_5',
                    $this->table.'.public_return_editor_department_code_1',
                    $this->table.'.public_return_editor_department_code_2',
                    $this->table.'.public_return_editor_department_code_3',
                    $this->table.'.public_return_editor_department_code_4',
                    $this->table.'.public_return_editor_department_code_5',
                    $this->table.'.public_return_editor_department_name_1',
                    $this->table.'.public_return_editor_department_name_2',
                    $this->table.'.public_return_editor_department_name_3',
                    $this->table.'.public_return_editor_department_name_4',
                    $this->table.'.public_return_editor_department_name_5',
                    $this->table.'.public_return_editor_user_code_1',
                    $this->table.'.public_return_editor_user_code_2',
                    $this->table.'.public_return_editor_user_code_3',
                    $this->table.'.public_return_editor_user_code_4',
                    $this->table.'.public_return_editor_user_code_5',
                    $this->table.'.public_return_editor_user_name_1',
                    $this->table.'.public_return_editor_user_name_2',
                    $this->table.'.public_return_editor_user_name_3',
                    $this->table.'.public_return_editor_user_name_4',
                    $this->table.'.public_return_editor_user_name_5',
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
                    $this->table.'.legal_working_holiday_hours',
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
                    $this->table.'.fixedtime',
                    $this->table.'.created_user',
                    $this->table.'.updated_user',
                    $this->table.'.is_deleted');
            $mainquery
                ->selectRaw('X('.$this->table.'.attendance_time_positions_1) as x_attendance_time_positions_1')
                ->selectRaw('Y('.$this->table.'.attendance_time_positions_1) as y_attendance_time_positions_1')
                ->selectRaw('X('.$this->table.'.attendance_time_positions_2) as x_attendance_time_positions_2')
                ->selectRaw('Y('.$this->table.'.attendance_time_positions_2) as y_attendance_time_positions_2')
                ->selectRaw('X('.$this->table.'.attendance_time_positions_3) as x_attendance_time_positions_3')
                ->selectRaw('Y('.$this->table.'.attendance_time_positions_3) as y_attendance_time_positions_3')
                ->selectRaw('X('.$this->table.'.attendance_time_positions_4) as x_attendance_time_positions_4')
                ->selectRaw('Y('.$this->table.'.attendance_time_positions_4) as y_attendance_time_positions_4')
                ->selectRaw('X('.$this->table.'.attendance_time_positions_5) as x_attendance_time_positions_5')
                ->selectRaw('Y('.$this->table.'.attendance_time_positions_5) as y_attendance_time_positions_5')
                ->selectRaw('X('.$this->table.'.leaving_time_positions_1) as x_leaving_time_positions_1')
                ->selectRaw('Y('.$this->table.'.leaving_time_positions_1) as y_leaving_time_positions_1')
                ->selectRaw('X('.$this->table.'.leaving_time_positions_2) as x_leaving_time_positions_2')
                ->selectRaw('Y('.$this->table.'.leaving_time_positions_2) as y_leaving_time_positions_2')
                ->selectRaw('X('.$this->table.'.leaving_time_positions_3) as x_leaving_time_positions_3')
                ->selectRaw('Y('.$this->table.'.leaving_time_positions_3) as y_leaving_time_positions_3')
                ->selectRaw('X('.$this->table.'.leaving_time_positions_4) as x_leaving_time_positions_4')
                ->selectRaw('Y('.$this->table.'.leaving_time_positions_4) as y_leaving_time_positions_4')
                ->selectRaw('X('.$this->table.'.leaving_time_positions_5) as x_leaving_time_positions_5')
                ->selectRaw('Y('.$this->table.'.leaving_time_positions_5) as y_leaving_time_positions_5')
                ->selectRaw('X('.$this->table.'.missing_middle_time_positions_1) as x_missing_middle_time_positions_1')
                ->selectRaw('Y('.$this->table.'.missing_middle_time_positions_1) as y_missing_middle_time_positions_1')
                ->selectRaw('X('.$this->table.'.missing_middle_time_positions_2) as x_missing_middle_time_positions_2')
                ->selectRaw('Y('.$this->table.'.missing_middle_time_positions_2) as y_missing_middle_time_positions_2')
                ->selectRaw('X('.$this->table.'.missing_middle_time_positions_3) as x_missing_middle_time_positions_3')
                ->selectRaw('Y('.$this->table.'.missing_middle_time_positions_3) as y_missing_middle_time_positions_3')
                ->selectRaw('X('.$this->table.'.missing_middle_time_positions_4) as x_missing_middle_time_positions_4')
                ->selectRaw('Y('.$this->table.'.missing_middle_time_positions_4) as y_missing_middle_time_positions_4')
                ->selectRaw('X('.$this->table.'.missing_middle_time_positions_5) as x_missing_middle_time_positions_5')
                ->selectRaw('Y('.$this->table.'.missing_middle_time_positions_5) as y_missing_middle_time_positions_5')
                ->selectRaw('X('.$this->table.'.missing_middle_return_time_positions_1) as x_missing_middle_return_time_positions_1')
                ->selectRaw('Y('.$this->table.'.missing_middle_return_time_positions_1) as y_missing_middle_return_time_positions_1')
                ->selectRaw('X('.$this->table.'.missing_middle_return_time_positions_2) as x_missing_middle_return_time_positions_2')
                ->selectRaw('Y('.$this->table.'.missing_middle_return_time_positions_2) as y_missing_middle_return_time_positions_2')
                ->selectRaw('X('.$this->table.'.missing_middle_return_time_positions_3) as x_missing_middle_return_time_positions_3')
                ->selectRaw('Y('.$this->table.'.missing_middle_return_time_positions_3) as y_missing_middle_return_time_positions_3')
                ->selectRaw('X('.$this->table.'.missing_middle_return_time_positions_4) as x_missing_middle_return_time_positions_4')
                ->selectRaw('Y('.$this->table.'.missing_middle_return_time_positions_4) as y_missing_middle_return_time_positions_4')
                ->selectRaw('X('.$this->table.'.missing_middle_return_time_positions_5) as x_missing_middle_return_time_positions_5')
                ->selectRaw('Y('.$this->table.'.missing_middle_return_time_positions_5) as y_missing_middle_return_time_positions_5')
                ->selectRaw('X('.$this->table.'.public_going_out_time_positions_1) as x_public_going_out_time_positions_1')
                ->selectRaw('Y('.$this->table.'.public_going_out_time_positions_1) as y_public_going_out_time_positions_1')
                ->selectRaw('X('.$this->table.'.public_going_out_time_positions_2) as x_public_going_out_time_positions_2')
                ->selectRaw('Y('.$this->table.'.public_going_out_time_positions_2) as y_public_going_out_time_positions_2')
                ->selectRaw('X('.$this->table.'.public_going_out_time_positions_3) as x_public_going_out_time_positions_3')
                ->selectRaw('Y('.$this->table.'.public_going_out_time_positions_3) as y_public_going_out_time_positions_3')
                ->selectRaw('X('.$this->table.'.public_going_out_time_positions_4) as x_public_going_out_time_positions_4')
                ->selectRaw('Y('.$this->table.'.public_going_out_time_positions_4) as y_public_going_out_time_positions_4')
                ->selectRaw('X('.$this->table.'.public_going_out_time_positions_5) as x_public_going_out_time_positions_5')
                ->selectRaw('Y('.$this->table.'.public_going_out_time_positions_5) as y_public_going_out_time_positions_5')
                ->selectRaw('X('.$this->table.'.public_going_out_return_time_positions_1) as x_public_going_out_return_time_positions_1')
                ->selectRaw('Y('.$this->table.'.public_going_out_return_time_positions_1) as y_public_going_out_return_time_positions_1')
                ->selectRaw('X('.$this->table.'.public_going_out_return_time_positions_2) as x_public_going_out_return_time_positions_2')
                ->selectRaw('Y('.$this->table.'.public_going_out_return_time_positions_2) as y_public_going_out_return_time_positions_2')
                ->selectRaw('X('.$this->table.'.public_going_out_return_time_positions_3) as x_public_going_out_return_time_positions_3')
                ->selectRaw('Y('.$this->table.'.public_going_out_return_time_positions_3) as y_public_going_out_return_time_positions_3')
                ->selectRaw('X('.$this->table.'.public_going_out_return_time_positions_4) as x_public_going_out_return_time_positions_4')
                ->selectRaw('Y('.$this->table.'.public_going_out_return_time_positions_4) as y_public_going_out_return_time_positions_4')
                ->selectRaw('X('.$this->table.'.public_going_out_return_time_positions_5) as x_public_going_out_return_time_positions_5')
                ->selectRaw('Y('.$this->table.'.public_going_out_return_time_positions_5) as y_public_going_out_return_time_positions_5');

            if(!empty($this->param_date_from) && !empty($this->param_date_to)){
                $date = date_create($this->param_date_from);
                $this->param_date_from = $date->format('Ymd');
                $date = date_create($this->param_date_to);
                $this->param_date_to = $date->format('Ymd');
                $mainquery->where($this->table.'.working_date', '>=', $this->param_date_from);          // 日付範囲指定
                $mainquery->where($this->table.'.working_date', '<=', $this->param_date_to);            // 日付範囲指定
            }
            
            if(!empty($this->param_employment_status)){
                $mainquery->where($this->table.'.employment_status', $this->param_employment_status);   //employment_status指定
            }
            
            if(!empty($this->param_user_code)){
                $mainquery->where($this->table.'.user_code', $this->param_user_code);                   //user_code指定
            }
            
            if(!empty($this->param_department_code)){
                $mainquery->where($this->table.'.department_code', $this->param_department_code);       //department_code指定
            }
            $result = $mainquery->where('t1.is_deleted', '=', 0)->get();
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
    public function getWorkingTimeDateTimeFormat($dayormonth, $targetdate, $business_kubun){


        // 日次労働時間取得SQL作成
        $result = true;

        try{
            $case_where = "CASE ifnull({0},0) WHEN 0 THEN '00:00' ";
            $case_where .= "ELSE CONCAT(CONCAT(LPAD(TRUNCATE({0}, 0), 2, '0'),':'),LPAD(TRUNCATE((mod({0} * 100, 100) * 60) / 100, 0) , 2, '0')) ";
            $case_where .= ' END as {1}';

            $mainquery = DB::table($this->table_users)
                ->selectRaw($this->table_calendars.'.date as working_date');
            $mainquery
                ->addselect(
                    $this->table_users.'.employment_status',
                    $this->table_users.'.department_code',
                    $this->table_users.'.code');
            /*$mainquery
                ->selectRaw('ifnull('.$this->table.".employment_status_name,'　')  as employment_status_name")
                ->selectRaw('ifnull('.$this->table.".department_name,'　')  as department_name")
                ->selectRaw('ifnull('.$this->table.".user_name,'　')  as user_name"); */
            $mainquery
                ->selectRaw($this->table_users.'.code as user_code')
                ->selectRaw("ifnull(t1.code_name,'　')  as employment_status_name")
                ->selectRaw("ifnull(t21.name,'　')  as department_name")
                ->selectRaw("ifnull(".$this->table_users.".name,'　')  as user_name");
            $mainquery
                ->addselect($this->table.'.working_timetable_no');
            $mainquery
                ->selectRaw('ifnull('.$this->table.".working_timetable_name,'　')  as working_timetable_name");
            $mainquery
                ->selectRaw('DATE_FORMAT('.$this->table.'.attendance_time_1,'."'%H:%i'".')  as attendance_time_1')
                ->selectRaw('DATE_FORMAT('.$this->table.'.attendance_time_2,'."'%H:%i'".')  as attendance_time_2')
                ->selectRaw('DATE_FORMAT('.$this->table.'.attendance_time_3,'."'%H:%i'".')  as attendance_time_3')
                ->selectRaw('DATE_FORMAT('.$this->table.'.attendance_time_4,'."'%H:%i'".')  as attendance_time_4')
                ->selectRaw('DATE_FORMAT('.$this->table.'.attendance_time_5,'."'%H:%i'".')  as attendance_time_5')
                ->selectRaw('DATE_FORMAT('.$this->table.'.leaving_time_1,'."'%H:%i'".')  as leaving_time_1')
                ->selectRaw('DATE_FORMAT('.$this->table.'.leaving_time_2,'."'%H:%i'".')  as leaving_time_2')
                ->selectRaw('DATE_FORMAT('.$this->table.'.leaving_time_3,'."'%H:%i'".')  as leaving_time_3')
                ->selectRaw('DATE_FORMAT('.$this->table.'.leaving_time_4,'."'%H:%i'".')  as leaving_time_4')
                ->selectRaw('DATE_FORMAT('.$this->table.'.leaving_time_5,'."'%H:%i'".')  as leaving_time_5')
                ->selectRaw('DATE_FORMAT('.$this->table.'.missing_middle_time_1,'."'%H:%i'".')  as missing_middle_time_1')
                ->selectRaw('DATE_FORMAT('.$this->table.'.missing_middle_time_2,'."'%H:%i'".')  as missing_middle_time_2')
                ->selectRaw('DATE_FORMAT('.$this->table.'.missing_middle_time_3,'."'%H:%i'".')  as missing_middle_time_3')
                ->selectRaw('DATE_FORMAT('.$this->table.'.missing_middle_time_4,'."'%H:%i'".')  as missing_middle_time_4')
                ->selectRaw('DATE_FORMAT('.$this->table.'.missing_middle_time_5,'."'%H:%i'".')  as missing_middle_time_5')
                ->selectRaw('DATE_FORMAT('.$this->table.'.missing_middle_return_time_1,'."'%H:%i'".')  as missing_middle_return_time_1')
                ->selectRaw('DATE_FORMAT('.$this->table.'.missing_middle_return_time_2,'."'%H:%i'".')  as missing_middle_return_time_2')
                ->selectRaw('DATE_FORMAT('.$this->table.'.missing_middle_return_time_3,'."'%H:%i'".')  as missing_middle_return_time_3')
                ->selectRaw('DATE_FORMAT('.$this->table.'.missing_middle_return_time_4,'."'%H:%i'".')  as missing_middle_return_time_4')
                ->selectRaw('DATE_FORMAT('.$this->table.'.missing_middle_return_time_5,'."'%H:%i'".')  as missing_middle_return_time_5')
                ->selectRaw('DATE_FORMAT('.$this->table.'.public_going_out_time_1,'."'%H:%i'".')  as public_going_out_time_1')
                ->selectRaw('DATE_FORMAT('.$this->table.'.public_going_out_time_2,'."'%H:%i'".')  as public_going_out_time_2')
                ->selectRaw('DATE_FORMAT('.$this->table.'.public_going_out_time_3,'."'%H:%i'".')  as public_going_out_time_3')
                ->selectRaw('DATE_FORMAT('.$this->table.'.public_going_out_time_4,'."'%H:%i'".')  as public_going_out_time_4')
                ->selectRaw('DATE_FORMAT('.$this->table.'.public_going_out_time_5,'."'%H:%i'".')  as public_going_out_time_5')
                ->selectRaw('DATE_FORMAT('.$this->table.'.public_going_out_return_time_1,'."'%H:%i'".')  as public_going_out_return_time_1')
                ->selectRaw('DATE_FORMAT('.$this->table.'.public_going_out_return_time_2,'."'%H:%i'".')  as public_going_out_return_time_2')
                ->selectRaw('DATE_FORMAT('.$this->table.'.public_going_out_return_time_3,'."'%H:%i'".')  as public_going_out_return_time_3')
                ->selectRaw('DATE_FORMAT('.$this->table.'.public_going_out_return_time_4,'."'%H:%i'".')  as public_going_out_return_time_4')
                ->selectRaw('DATE_FORMAT('.$this->table.'.public_going_out_return_time_5,'."'%H:%i'".')  as public_going_out_return_time_5')
                ->selectRaw(str_replace('{1}', 'total_working_times', str_replace('{0}', $this->table.'.total_working_times', $case_where)))
                ->selectRaw(str_replace('{1}', 'regular_working_times', str_replace('{0}', $this->table.'.regular_working_times', $case_where)))
                ->selectRaw(str_replace('{1}', 'out_of_regular_working_times', str_replace('{0}', $this->table.'.out_of_regular_working_times', $case_where)))
                ->selectRaw(str_replace('{1}', 'overtime_hours', str_replace('{0}', $this->table.'.overtime_hours', $case_where)))
                ->selectRaw(str_replace('{1}', 'late_night_overtime_hours', str_replace('{0}', $this->table.'.late_night_overtime_hours', $case_where)))
                ->selectRaw(str_replace('{1}', 'late_night_working_hours', str_replace('{0}', $this->table.'.late_night_working_hours', $case_where)))
                ->selectRaw(str_replace('{1}', 'legal_working_times', str_replace('{0}', $this->table.'.legal_working_times', $case_where)))
                ->selectRaw(str_replace('{1}', 'out_of_legal_working_times', str_replace('{0}', $this->table.'.out_of_legal_working_times', $case_where)))
                ->selectRaw(str_replace('{1}', 'not_employment_working_hours', str_replace('{0}', $this->table.'.not_employment_working_hours', $case_where)))
                ->selectRaw(str_replace('{1}', 'off_hours_working_hours', str_replace('{0}', $this->table.'.off_hours_working_hours', $case_where)))
                ->selectRaw(str_replace('{1}', 'public_going_out_hours', str_replace('{0}', $this->table.'.public_going_out_hours', $case_where)))
                ->selectRaw(str_replace('{1}', 'missing_middle_hours', str_replace('{0}', $this->table.'.missing_middle_hours', $case_where)))
                ->selectRaw(str_replace('{1}', 'out_of_legal_working_holiday_hours', str_replace('{0}', $this->table.'.out_of_legal_working_holiday_hours', $case_where)))
                ->selectRaw(str_replace('{1}', 'legal_working_holiday_hours', str_replace('{0}', $this->table.'.legal_working_holiday_hours', $case_where)))
                ->selectRaw('X('.$this->table.'.attendance_time_positions_1) as x_attendance_time_positions_1')
                ->selectRaw('Y('.$this->table.'.attendance_time_positions_1) as y_attendance_time_positions_1')
                ->selectRaw('X('.$this->table.'.attendance_time_positions_2) as x_attendance_time_positions_2')
                ->selectRaw('Y('.$this->table.'.attendance_time_positions_2) as y_attendance_time_positions_2')
                ->selectRaw('X('.$this->table.'.attendance_time_positions_3) as x_attendance_time_positions_3')
                ->selectRaw('Y('.$this->table.'.attendance_time_positions_3) as y_attendance_time_positions_3')
                ->selectRaw('X('.$this->table.'.attendance_time_positions_4) as x_attendance_time_positions_4')
                ->selectRaw('Y('.$this->table.'.attendance_time_positions_4) as y_attendance_time_positions_4')
                ->selectRaw('X('.$this->table.'.attendance_time_positions_5) as x_attendance_time_positions_5')
                ->selectRaw('Y('.$this->table.'.attendance_time_positions_5) as y_attendance_time_positions_5')
                ->selectRaw('X('.$this->table.'.leaving_time_positions_1) as x_leaving_time_positions_1')
                ->selectRaw('Y('.$this->table.'.leaving_time_positions_1) as y_leaving_time_positions_1')
                ->selectRaw('X('.$this->table.'.leaving_time_positions_2) as x_leaving_time_positions_2')
                ->selectRaw('Y('.$this->table.'.leaving_time_positions_2) as y_leaving_time_positions_2')
                ->selectRaw('X('.$this->table.'.leaving_time_positions_3) as x_leaving_time_positions_3')
                ->selectRaw('Y('.$this->table.'.leaving_time_positions_3) as y_leaving_time_positions_3')
                ->selectRaw('X('.$this->table.'.leaving_time_positions_4) as x_leaving_time_positions_4')
                ->selectRaw('Y('.$this->table.'.leaving_time_positions_4) as y_leaving_time_positions_4')
                ->selectRaw('X('.$this->table.'.leaving_time_positions_5) as x_leaving_time_positions_5')
                ->selectRaw('Y('.$this->table.'.leaving_time_positions_5) as y_leaving_time_positions_5')
                ->selectRaw('X('.$this->table.'.missing_middle_time_positions_1) as x_missing_middle_time_positions_1')
                ->selectRaw('Y('.$this->table.'.missing_middle_time_positions_1) as y_missing_middle_time_positions_1')
                ->selectRaw('X('.$this->table.'.missing_middle_time_positions_2) as x_missing_middle_time_positions_2')
                ->selectRaw('Y('.$this->table.'.missing_middle_time_positions_2) as y_missing_middle_time_positions_2')
                ->selectRaw('X('.$this->table.'.missing_middle_time_positions_3) as x_missing_middle_time_positions_3')
                ->selectRaw('Y('.$this->table.'.missing_middle_time_positions_3) as y_missing_middle_time_positions_3')
                ->selectRaw('X('.$this->table.'.missing_middle_time_positions_4) as x_missing_middle_time_positions_4')
                ->selectRaw('Y('.$this->table.'.missing_middle_time_positions_4) as y_missing_middle_time_positions_4')
                ->selectRaw('X('.$this->table.'.missing_middle_time_positions_5) as x_missing_middle_time_positions_5')
                ->selectRaw('Y('.$this->table.'.missing_middle_time_positions_5) as y_missing_middle_time_positions_5')
                ->selectRaw('X('.$this->table.'.missing_middle_return_time_positions_1) as x_missing_middle_return_time_positions_1')
                ->selectRaw('Y('.$this->table.'.missing_middle_return_time_positions_1) as y_missing_middle_return_time_positions_1')
                ->selectRaw('X('.$this->table.'.missing_middle_return_time_positions_2) as x_missing_middle_return_time_positions_2')
                ->selectRaw('Y('.$this->table.'.missing_middle_return_time_positions_2) as y_missing_middle_return_time_positions_2')
                ->selectRaw('X('.$this->table.'.missing_middle_return_time_positions_3) as x_missing_middle_return_time_positions_3')
                ->selectRaw('Y('.$this->table.'.missing_middle_return_time_positions_3) as y_missing_middle_return_time_positions_3')
                ->selectRaw('X('.$this->table.'.missing_middle_return_time_positions_4) as x_missing_middle_return_time_positions_4')
                ->selectRaw('Y('.$this->table.'.missing_middle_return_time_positions_4) as y_missing_middle_return_time_positions_4')
                ->selectRaw('X('.$this->table.'.missing_middle_return_time_positions_5) as x_missing_middle_return_time_positions_5')
                ->selectRaw('Y('.$this->table.'.missing_middle_return_time_positions_5) as y_missing_middle_return_time_positions_5')
                ->selectRaw('X('.$this->table.'.public_going_out_time_positions_1) as x_public_going_out_time_positions_1')
                ->selectRaw('Y('.$this->table.'.public_going_out_time_positions_1) as y_public_going_out_time_positions_1')
                ->selectRaw('X('.$this->table.'.public_going_out_time_positions_2) as x_public_going_out_time_positions_2')
                ->selectRaw('Y('.$this->table.'.public_going_out_time_positions_2) as y_public_going_out_time_positions_2')
                ->selectRaw('X('.$this->table.'.public_going_out_time_positions_3) as x_public_going_out_time_positions_3')
                ->selectRaw('Y('.$this->table.'.public_going_out_time_positions_3) as y_public_going_out_time_positions_3')
                ->selectRaw('X('.$this->table.'.public_going_out_time_positions_4) as x_public_going_out_time_positions_4')
                ->selectRaw('Y('.$this->table.'.public_going_out_time_positions_4) as y_public_going_out_time_positions_4')
                ->selectRaw('X('.$this->table.'.public_going_out_time_positions_5) as x_public_going_out_time_positions_5')
                ->selectRaw('Y('.$this->table.'.public_going_out_time_positions_5) as y_public_going_out_time_positions_5')
                ->selectRaw('X('.$this->table.'.public_going_out_return_time_positions_1) as x_public_going_out_return_time_positions_1')
                ->selectRaw('Y('.$this->table.'.public_going_out_return_time_positions_1) as y_public_going_out_return_time_positions_1')
                ->selectRaw('X('.$this->table.'.public_going_out_return_time_positions_2) as x_public_going_out_return_time_positions_2')
                ->selectRaw('Y('.$this->table.'.public_going_out_return_time_positions_2) as y_public_going_out_return_time_positions_2')
                ->selectRaw('X('.$this->table.'.public_going_out_return_time_positions_3) as x_public_going_out_return_time_positions_3')
                ->selectRaw('Y('.$this->table.'.public_going_out_return_time_positions_3) as y_public_going_out_return_time_positions_3')
                ->selectRaw('X('.$this->table.'.public_going_out_return_time_positions_4) as x_public_going_out_return_time_positions_4')
                ->selectRaw('Y('.$this->table.'.public_going_out_return_time_positions_4) as y_public_going_out_return_time_positions_4')
                ->selectRaw('X('.$this->table.'.public_going_out_return_time_positions_5) as x_public_going_out_return_time_positions_5')
                ->selectRaw('Y('.$this->table.'.public_going_out_return_time_positions_5) as y_public_going_out_return_time_positions_5');

            $mainquery
                ->addselect($this->table.'.working_status');
            // $remarks_date_holiday_name = ' CASE ifnull('.$this->table.".holiday_name, '') WHEN '' THEN '' ELSE ".$this->table.'.holiday_name END as remark_holiday_name'; 
            $remarks_date_holiday_name = 't2.code_name as remark_holiday_name'; 
            $remarks_date_check_result = ' CASE ifnull('.$this->table.'.check_result, 0)';
            $remarks_date_check_result .= ' WHEN '.Config::get('const.C018.forget_stamp')." THEN '".Config::get('const.C018_NAME.forget_stamp')."' ";
            $remarks_date_check_result .= ' WHEN '.Config::get('const.C018.interval_stamp')." THEN '".Config::get('const.C018_NAME.interval_stamp')."' ";
            $remarks_date_check_result .= ' WHEN '.Config::get('const.C018.no_leave_apply')." THEN '".Config::get('const.C018_NAME.no_leave_apply')."' ";
            $remarks_date_check_result .= " ELSE '' END as remark_check_result"; 
            $remarks_date_check_max_times = ' CASE ifnull('.$this->table.'.check_max_times, 0)';
            $remarks_date_check_max_times .= ' WHEN '.Config::get('const.C018.max_time_over')." THEN '".Config::get('const.C018_NAME.max_time_over')."' ";
            $remarks_date_check_max_times .= " ELSE '' END as remark_check_max_times"; 
            $remarks_date_check_interval = ' CASE ifnull('.$this->table.'.check_interval, 0)';
            $remarks_date_check_interval .= ' WHEN '.Config::get('const.C018.interval_stamp')." THEN '".Config::get('const.C018_NAME.interval_stamp')."' ";
            $remarks_date_check_interval .= " ELSE '' END as remark_check_interval"; 
            
            $mainquery
                ->selectRaw('ifnull('.$this->table.".working_status_name,'　')  as working_status_name")
                ->selectRaw($remarks_date_holiday_name)
                ->selectRaw($remarks_date_check_result)
                ->selectRaw($remarks_date_check_max_times)
                ->selectRaw($remarks_date_check_interval);
            $mainquery
                ->addselect($this->table.'.note')
                ->addselect($this->table.'.late')
                ->addselect($this->table.'.leave_early')
                ->addselect($this->table.'.current_calc')
                ->addselect($this->table.'.to_be_confirmed')
                ->addselect($this->table.'.weekday_kubun');
            $mainquery
                ->selectRaw('ifnull('.$this->table.".weekday_name,'　')  as weekday_name");
            $mainquery
                ->addselect($this->table.'.business_kubun');
            $mainquery
                ->selectRaw('ifnull('.$this->table.".business_name,'　')  as business_name");
            $mainquery
                ->addselect($this->table.'.holiday_kubun as unused_holiday_kubun');
            $mainquery
                ->selectRaw('ifnull('.$this->table.".holiday_name,'　') as unused_holiday_name");
            $mainquery
                ->addselect($this->table.'.attendance_time_id_1')
                ->addselect($this->table.'.attendance_time_id_2')
                ->addselect($this->table.'.attendance_time_id_3')
                ->addselect($this->table.'.attendance_time_id_4')
                ->addselect($this->table.'.attendance_time_id_5')
                ->addselect($this->table.'.attendance_editor_department_code_1')
                ->addselect($this->table.'.attendance_editor_department_code_2')
                ->addselect($this->table.'.attendance_editor_department_code_3')
                ->addselect($this->table.'.attendance_editor_department_code_4')
                ->addselect($this->table.'.attendance_editor_department_code_5')
                ->addselect($this->table.'.attendance_editor_department_name_1')
                ->addselect($this->table.'.attendance_editor_department_name_2')
                ->addselect($this->table.'.attendance_editor_department_name_3')
                ->addselect($this->table.'.attendance_editor_department_name_4')
                ->addselect($this->table.'.attendance_editor_department_name_5')
                ->addselect($this->table.'.attendance_editor_user_code_1')
                ->addselect($this->table.'.attendance_editor_user_code_2')
                ->addselect($this->table.'.attendance_editor_user_code_3')
                ->addselect($this->table.'.attendance_editor_user_code_4')
                ->addselect($this->table.'.attendance_editor_user_code_5')
                ->addselect($this->table.'.attendance_editor_user_name_1')
                ->addselect($this->table.'.attendance_editor_user_name_2')
                ->addselect($this->table.'.attendance_editor_user_name_3')
                ->addselect($this->table.'.attendance_editor_user_name_4')
                ->addselect($this->table.'.attendance_editor_user_name_5')
                ->addselect($this->table.'.leaving_time_id_1')
                ->addselect($this->table.'.leaving_time_id_2')
                ->addselect($this->table.'.leaving_time_id_3')
                ->addselect($this->table.'.leaving_time_id_4')
                ->addselect($this->table.'.leaving_time_id_5')
                ->addselect($this->table.'.leaving_editor_department_code_1')
                ->addselect($this->table.'.leaving_editor_department_code_2')
                ->addselect($this->table.'.leaving_editor_department_code_3')
                ->addselect($this->table.'.leaving_editor_department_code_4')
                ->addselect($this->table.'.leaving_editor_department_code_5')
                ->addselect($this->table.'.leaving_editor_department_name_1')
                ->addselect($this->table.'.leaving_editor_department_name_2')
                ->addselect($this->table.'.leaving_editor_department_name_3')
                ->addselect($this->table.'.leaving_editor_department_name_4')
                ->addselect($this->table.'.leaving_editor_department_name_5')
                ->addselect($this->table.'.leaving_editor_user_code_1')
                ->addselect($this->table.'.leaving_editor_user_code_2')
                ->addselect($this->table.'.leaving_editor_user_code_3')
                ->addselect($this->table.'.leaving_editor_user_code_4')
                ->addselect($this->table.'.leaving_editor_user_code_5')
                ->addselect($this->table.'.leaving_editor_user_name_1')
                ->addselect($this->table.'.leaving_editor_user_name_2')
                ->addselect($this->table.'.leaving_editor_user_name_3')
                ->addselect($this->table.'.leaving_editor_user_name_4')
                ->addselect($this->table.'.leaving_editor_user_name_5')
                ->addselect($this->table.'.missing_middle_time_id_1')
                ->addselect($this->table.'.missing_middle_time_id_2')
                ->addselect($this->table.'.missing_middle_time_id_3')
                ->addselect($this->table.'.missing_middle_time_id_4')
                ->addselect($this->table.'.missing_middle_time_id_5')
                ->addselect($this->table.'.missing_editor_department_code_1')
                ->addselect($this->table.'.missing_editor_department_code_2')
                ->addselect($this->table.'.missing_editor_department_code_3')
                ->addselect($this->table.'.missing_editor_department_code_4')
                ->addselect($this->table.'.missing_editor_department_code_5')
                ->addselect($this->table.'.missing_editor_department_name_1')
                ->addselect($this->table.'.missing_editor_department_name_2')
                ->addselect($this->table.'.missing_editor_department_name_3')
                ->addselect($this->table.'.missing_editor_department_name_4')
                ->addselect($this->table.'.missing_editor_department_name_5')
                ->addselect($this->table.'.missing_editor_user_code_1')
                ->addselect($this->table.'.missing_editor_user_code_2')
                ->addselect($this->table.'.missing_editor_user_code_3')
                ->addselect($this->table.'.missing_editor_user_code_4')
                ->addselect($this->table.'.missing_editor_user_code_5')
                ->addselect($this->table.'.missing_editor_user_name_1')
                ->addselect($this->table.'.missing_editor_user_name_2')
                ->addselect($this->table.'.missing_editor_user_name_3')
                ->addselect($this->table.'.missing_editor_user_name_4')
                ->addselect($this->table.'.missing_editor_user_name_5')
                ->addselect($this->table.'.missing_middle_return_time_id_1')
                ->addselect($this->table.'.missing_middle_return_time_id_2')
                ->addselect($this->table.'.missing_middle_return_time_id_3')
                ->addselect($this->table.'.missing_middle_return_time_id_4')
                ->addselect($this->table.'.missing_middle_return_time_id_5')
                ->addselect($this->table.'.missing_return_editor_department_code_1')
                ->addselect($this->table.'.missing_return_editor_department_code_2')
                ->addselect($this->table.'.missing_return_editor_department_code_3')
                ->addselect($this->table.'.missing_return_editor_department_code_4')
                ->addselect($this->table.'.missing_return_editor_department_code_5')
                ->addselect($this->table.'.missing_return_editor_department_name_1')
                ->addselect($this->table.'.missing_return_editor_department_name_2')
                ->addselect($this->table.'.missing_return_editor_department_name_3')
                ->addselect($this->table.'.missing_return_editor_department_name_4')
                ->addselect($this->table.'.missing_return_editor_department_name_5')
                ->addselect($this->table.'.missing_return_editor_user_code_1')
                ->addselect($this->table.'.missing_return_editor_user_code_2')
                ->addselect($this->table.'.missing_return_editor_user_code_3')
                ->addselect($this->table.'.missing_return_editor_user_code_4')
                ->addselect($this->table.'.missing_return_editor_user_code_5')
                ->addselect($this->table.'.missing_return_editor_user_name_1')
                ->addselect($this->table.'.missing_return_editor_user_name_2')
                ->addselect($this->table.'.missing_return_editor_user_name_3')
                ->addselect($this->table.'.missing_return_editor_user_name_4')
                ->addselect($this->table.'.missing_return_editor_user_name_5')
                ->addselect($this->table.'.public_going_out_time_id_1')
                ->addselect($this->table.'.public_going_out_time_id_2')
                ->addselect($this->table.'.public_going_out_time_id_3')
                ->addselect($this->table.'.public_going_out_time_id_4')
                ->addselect($this->table.'.public_going_out_time_id_5')
                ->addselect($this->table.'.public_editor_department_code_1')
                ->addselect($this->table.'.public_editor_department_code_2')
                ->addselect($this->table.'.public_editor_department_code_3')
                ->addselect($this->table.'.public_editor_department_code_4')
                ->addselect($this->table.'.public_editor_department_code_5')
                ->addselect($this->table.'.public_editor_department_name_1')
                ->addselect($this->table.'.public_editor_department_name_2')
                ->addselect($this->table.'.public_editor_department_name_3')
                ->addselect($this->table.'.public_editor_department_name_4')
                ->addselect($this->table.'.public_editor_department_name_5')
                ->addselect($this->table.'.public_editor_user_code_1')
                ->addselect($this->table.'.public_editor_user_code_2')
                ->addselect($this->table.'.public_editor_user_code_3')
                ->addselect($this->table.'.public_editor_user_code_4')
                ->addselect($this->table.'.public_editor_user_code_5')
                ->addselect($this->table.'.public_editor_user_name_1')
                ->addselect($this->table.'.public_editor_user_name_2')
                ->addselect($this->table.'.public_editor_user_name_3')
                ->addselect($this->table.'.public_editor_user_name_4')
                ->addselect($this->table.'.public_editor_user_name_5')
                ->addselect($this->table.'.public_going_out_return_time_id_1')
                ->addselect($this->table.'.public_going_out_return_time_id_2')
                ->addselect($this->table.'.public_going_out_return_time_id_3')
                ->addselect($this->table.'.public_going_out_return_time_id_4')
                ->addselect($this->table.'.public_going_out_return_time_id_5')
                ->addselect($this->table.'.public_return_editor_department_code_1')
                ->addselect($this->table.'.public_return_editor_department_code_2')
                ->addselect($this->table.'.public_return_editor_department_code_3')
                ->addselect($this->table.'.public_return_editor_department_code_4')
                ->addselect($this->table.'.public_return_editor_department_code_5')
                ->addselect($this->table.'.public_return_editor_department_name_1')
                ->addselect($this->table.'.public_return_editor_department_name_2')
                ->addselect($this->table.'.public_return_editor_department_name_3')
                ->addselect($this->table.'.public_return_editor_department_name_4')
                ->addselect($this->table.'.public_return_editor_department_name_5')
                ->addselect($this->table.'.public_return_editor_user_code_1')
                ->addselect($this->table.'.public_return_editor_user_code_2')
                ->addselect($this->table.'.public_return_editor_user_code_3')
                ->addselect($this->table.'.public_return_editor_user_code_4')
                ->addselect($this->table.'.public_return_editor_user_code_5')
                ->addselect($this->table.'.public_return_editor_user_name_1')
                ->addselect($this->table.'.public_return_editor_user_name_2')
                ->addselect($this->table.'.public_return_editor_user_name_3')
                ->addselect($this->table.'.public_return_editor_user_name_4')
                ->addselect($this->table.'.public_return_editor_user_name_5')
                ->addselect($this->table.'.closing')
                ->addselect($this->table.'.uplimit_time')
                ->addselect($this->table.'.statutory_uplimit_time')
                ->addselect($this->table.'.time_unit')
                ->addselect($this->table.'.time_rounding')
                ->addselect($this->table.'.max_3month_total')
                ->addselect($this->table.'.max_6month_total')
                ->addselect($this->table.'.max_12month_total')
                ->addselect($this->table.'.beginning_month')
                ->addselect($this->table.'.year')
                ->addselect($this->table.'.pattern')
                ->addselect($this->table.'.check_result')
                ->addselect($this->table.'.check_max_times')
                ->addselect($this->table.'.check_interval')
                ->addselect($this->table.'.fixedtime')
                ->addselect($this->table.'.created_user')
                ->addselect($this->table.'.updated_user')
                ->addselect($this->table.'.is_deleted')
                ->addselect($this->table_user_holiday_kubuns.'.holiday_kubun');
            $mainquery
                ->selectRaw('t2.code_name as holiday_name');
            $mainquery
                ->addselect($this->table_calendars.'.business_kubun as calendars_business_kubun');
                
            $case_where = "CASE ifnull({0},{1}) WHEN {1} THEN '{2}' ";
            $case_where .= " WHEN {3} THEN '{4}' ";
            $case_where .= " WHEN {5} THEN '{6}' ";
            $case_where .= " ELSE '{2}' ";
            $case_where .= ' END as {7}';

            $case_where_working_time_name = str_replace('{0}',$this->table_calendars.'.business_kubun', $case_where);
            $case_where_working_time_name = str_replace('{1}', Config::get('const.C007.basic'), $case_where_working_time_name);
            $case_where_working_time_name = str_replace('{2}', Config::get('const.WORKING_TIME_NAME.basic'), $case_where_working_time_name);
            $case_where_working_time_name = str_replace('{3}', Config::get('const.C007.legal_holoday'), $case_where_working_time_name);
            $case_where_working_time_name = str_replace('{4}', Config::get('const.WORKING_TIME_NAME.legal_holoday'), $case_where_working_time_name);
            $case_where_working_time_name = str_replace('{5}', Config::get('const.C007.legal_out_holoday'), $case_where_working_time_name);
            $case_where_working_time_name = str_replace('{6}', Config::get('const.WORKING_TIME_NAME.legal_out_holoday'), $case_where_working_time_name);
            $case_where_working_time_name = str_replace('{7}', 'working_time_name', $case_where_working_time_name);

            $case_where_predeter_time_name = str_replace('{0}',$this->table_calendars.'.business_kubun', $case_where);
            $case_where_predeter_time_name = str_replace('{1}', Config::get('const.C007.basic'), $case_where_predeter_time_name);
            $case_where_predeter_time_name = str_replace('{2}', Config::get('const.PREDETER_TIME_NAME.basic'), $case_where_predeter_time_name);
            $case_where_predeter_time_name = str_replace('{3}', Config::get('const.C007.legal_holoday'), $case_where_predeter_time_name);
            $case_where_predeter_time_name = str_replace('{4}', Config::get('const.PREDETER_TIME_NAME.legal_holoday'), $case_where_predeter_time_name);
            $case_where_predeter_time_name = str_replace('{5}', Config::get('const.C007.legal_out_holoday'), $case_where_predeter_time_name);
            $case_where_predeter_time_name = str_replace('{6}', Config::get('const.PREDETER_TIME_NAME.legal_out_holoday'), $case_where_predeter_time_name);
            $case_where_predeter_time_name = str_replace('{7}', 'predeter_time_name', $case_where_predeter_time_name);

            $case_where_predeter_time_secondname = str_replace('{0}',$this->table_calendars.'.business_kubun', $case_where);
            $case_where_predeter_time_secondname = str_replace('{1}', Config::get('const.C007.basic'), $case_where_predeter_time_secondname);
            $case_where_predeter_time_secondname = str_replace('{2}', Config::get('const.PREDETER_TIME_SECONDNAME.basic'), $case_where_predeter_time_secondname);
            $case_where_predeter_time_secondname = str_replace('{3}', Config::get('const.C007.legal_holoday'), $case_where_predeter_time_secondname);
            $case_where_predeter_time_secondname = str_replace('{4}', Config::get('const.PREDETER_TIME_SECONDNAME.legal_holoday'), $case_where_predeter_time_secondname);
            $case_where_predeter_time_secondname = str_replace('{5}', Config::get('const.C007.legal_out_holoday'), $case_where_predeter_time_secondname);
            $case_where_predeter_time_secondname = str_replace('{6}', Config::get('const.PREDETER_TIME_SECONDNAME.legal_out_holoday'), $case_where_predeter_time_secondname);
            $case_where_predeter_time_secondname = str_replace('{7}', 'predeter_time_secondname', $case_where_predeter_time_secondname);

            $case_where_predeter_night_time_name = str_replace('{0}',$this->table_calendars.'.business_kubun', $case_where);
            $case_where_predeter_night_time_name = str_replace('{1}', Config::get('const.C007.basic'), $case_where_predeter_night_time_name);
            $case_where_predeter_night_time_name = str_replace('{2}', Config::get('const.PREDETER_NIGHT_TIME_NAME.basic'), $case_where_predeter_night_time_name);
            $case_where_predeter_night_time_name = str_replace('{3}', Config::get('const.C007.legal_holoday'), $case_where_predeter_night_time_name);
            $case_where_predeter_night_time_name = str_replace('{4}', Config::get('const.PREDETER_NIGHT_TIME_NAME.legal_holoday'), $case_where_predeter_night_time_name);
            $case_where_predeter_night_time_name = str_replace('{5}', Config::get('const.C007.legal_out_holoday'), $case_where_predeter_night_time_name);
            $case_where_predeter_night_time_name = str_replace('{6}', Config::get('const.PREDETER_NIGHT_TIME_NAME.legal_out_holoday'), $case_where_predeter_night_time_name);
            $case_where_predeter_night_time_name = str_replace('{7}', 'predeter_night_time_name', $case_where_predeter_night_time_name);

            $case_where_predeter_night_time_secondname = str_replace('{0}',$this->table_calendars.'.business_kubun', $case_where);
            $case_where_predeter_night_time_secondname = str_replace('{1}', Config::get('const.C007.basic'), $case_where_predeter_night_time_secondname);
            $case_where_predeter_night_time_secondname = str_replace('{2}', Config::get('const.PREDETER_NIGHT_TIME_SECONDNAME.basic'), $case_where_predeter_night_time_secondname);
            $case_where_predeter_night_time_secondname = str_replace('{3}', Config::get('const.C007.legal_holoday'), $case_where_predeter_night_time_secondname);
            $case_where_predeter_night_time_secondname = str_replace('{4}', Config::get('const.PREDETER_NIGHT_TIME_SECONDNAME.legal_holoday'), $case_where_predeter_night_time_secondname);
            $case_where_predeter_night_time_secondname = str_replace('{5}', Config::get('const.C007.legal_out_holoday'), $case_where_predeter_night_time_secondname);
            $case_where_predeter_night_time_secondname = str_replace('{6}', Config::get('const.PREDETER_NIGHT_TIME_SECONDNAME.legal_out_holoday'), $case_where_predeter_night_time_secondname);
            $case_where_predeter_night_time_secondname = str_replace('{7}', 'predeter_night_time_secondname', $case_where_predeter_night_time_secondname);

            $mainquery
                ->selectRaw($case_where_working_time_name)
                ->selectRaw($case_where_predeter_time_name)
                ->selectRaw($case_where_predeter_time_secondname)
                ->selectRaw($case_where_predeter_night_time_name)
                ->selectRaw($case_where_predeter_night_time_secondname);

            // 適用期間日付の取得
            $apicommon = new ApiCommonController();
            // usersの最大適用開始日付subquery
            $subquery1 = $apicommon->getUserApplyTermSubquery($targetdate);
            // departmentsの最大適用開始日付subquery
            $subquery2 = $apicommon->getDepartmentApplyTermSubquery($targetdate);

            $mainquery
                ->leftJoin($this->table_calendars, function ($join) { 
                    $join->on($this->table_calendars.'.department_code', '=', $this->table_users.'.department_code');
                    $join->on($this->table_calendars.'.employment_status', '=', $this->table_users.'.employment_status');
                    $join->on($this->table_calendars.'.user_code', '=', $this->table_users.'.code')
                    ->where($this->table_calendars.'.is_deleted', '=', 0);
                })
                ->leftJoin($this->table, function ($join) { 
                    $join->on($this->table.'.department_code', '=', $this->table_users.'.department_code');
                    $join->on($this->table.'.user_code', '=', $this->table_users.'.code');
                    $join->on($this->table.'.working_date', '=', $this->table_calendars.'.date')
                    ->where($this->table.'.is_deleted', '=', 0);
                })
                ->leftJoin($this->table_user_holiday_kubuns, function ($join) { 
                    $join->on($this->table_user_holiday_kubuns.'.working_date', '=', $this->table_calendars.'.date');
                    $join->on($this->table_user_holiday_kubuns.'.department_code', '=', $this->table.'.department_code');
                    $join->on($this->table_user_holiday_kubuns.'.user_code', '=', $this->table.'.user_code')
                    ->where($this->table.'.is_deleted', '=', 0)
                    ->where($this->table_user_holiday_kubuns.'.is_deleted', '=', 0);
                })
                ->leftJoin($this->table_generalcodes.' as t1', function ($join) { 
                    $join->on('t1.code', '=', $this->table_users.'.employment_status')
                    ->where('t1.identification_id', '=', Config::get('const.C001.value'))
                    ->where('t1.is_deleted', '=', 0);
                })
                ->leftJoin($this->table_generalcodes.' as t2', function ($join) { 
                    $join->on('t2.code', '=', $this->table_user_holiday_kubuns.'.holiday_kubun')
                    ->where('t2.identification_id', '=', Config::get('const.C013.value'))
                    ->where('t2.is_deleted', '=', 0)
                    ->where($this->table_user_holiday_kubuns.'.is_deleted', '=', 0);
                });
            $mainquery
                ->leftJoinSub($subquery2, 't21', function ($join) { 
                    $join->on('t21.code', '=', $this->table_users.'.department_code')
                    ->where($this->table_users.'.is_deleted', '=', 0);
                });

            if ($dayormonth == Config::get('const.WORKINGTIME_DAY_OR_MONTH.daily_basic')) {
                if ($business_kubun != Config::get('const.C007.basic')) {
                    $mainquery
                        ->Join($this->table_temp_working_time_dates, function ($join) { 
                            $join->on($this->table_temp_working_time_dates.'.department_code', '=', $this->table_users.'.department_code');
                            $join->on($this->table_temp_working_time_dates.'.employment_status', '=', $this->table_users.'.employment_status');
                            $join->on($this->table_temp_working_time_dates.'.user_code', '=', $this->table_users.'.code');
                            $join->on($this->table_temp_working_time_dates.'.working_date', '=', $this->table_calendars.'.date');
                        })
                        ->WhereNotNull($this->table_temp_working_time_dates.'.attendance_time_1')
                        ->orWhereNotNull($this->table_temp_working_time_dates.'.leaving_time_1')
                        ->orWhereNotNull($this->table_temp_working_time_dates.'.missing_middle_time_1')
                        ->orWhereNotNull($this->table_temp_working_time_dates.'.missing_middle_return_time_1')
                        ->orWhereNotNull($this->table_temp_working_time_dates.'.public_going_out_time_1')
                        ->orWhereNotNull($this->table_temp_working_time_dates.'.public_going_out_return_time_1');
                }
                $mainquery = $this->setWhereSqlUsers($mainquery);
                $result = $mainquery
                    ->where($this->table.'.is_deleted', 0)
                    ->distinct()
                    ->orderBy($this->table_calendars.'.date', 'asc')
                    ->orderBy($this->table_users.'.department_code', 'asc')
                    ->orderBy($this->table_users.'.employment_status', 'asc')
                    ->orderBy($this->table_users.'.code', 'asc')
                    ->get();
            } elseif ($dayormonth == Config::get('const.WORKINGTIME_DAY_OR_MONTH.monthly_basic')) {
                $mainquery = $this->setWhereSqlUsers($mainquery);
                $result = $mainquery
                    ->distinct()
                    ->orderBy($this->table_users.'.department_code', 'asc')
                    ->orderBy($this->table_users.'.employment_status', 'asc')
                    ->orderBy($this->table_users.'.code', 'asc')
                    ->orderBy($this->table_calendars.'.date', 'asc')
                    ->get();
            }
            
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
     * 日次労働時間合計取得
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
    public function getWorkingTimeDateTimeSum($dayormonth){


        // 日次労働時間合計取得SQL作成
        try{
            $case_where = "CASE ifnull({0},0) WHEN 0 THEN '00:00' ";
            $case_where .= "ELSE CONCAT(CONCAT(TRUNCATE({0}, 0),':'),LPAD(TRUNCATE((mod({0} * 100, 100) * 60) / 100, 0) , 2, '0')) ";
            $case_where .= ' END as {1} ';

            $case_working_status = "CASE ifnull({0},0) WHEN 0 THEN 0 ";
            $case_working_status .= "WHEN {1} THEN 1 ";
            $case_working_status .= "WHEN {2} THEN 1 ";
            $case_working_status .= "WHEN {3} THEN 1 ";
            $case_working_status .= "WHEN {4} THEN 1 ";
            $case_working_status .= "WHEN {5} THEN 1 ";
            $case_working_status .= "WHEN {6} THEN 1 ";
            $case_working_status .= "WHEN {7} THEN 1 ";
            $case_working_status .= "ELSE 0 ";
            $case_working_status .= ' END ';

            $case_go_out = "CASE ifnull({0},0) WHEN 0 THEN 0 ";
            $case_go_out .= "WHEN {1} THEN 1 ";
            $case_go_out .= "WHEN {2} THEN 1 ";
            $case_go_out .= "ELSE 0 ";
            $case_go_out .= ' END ';

            $case_paid_holidays = "CASE ifnull({0},0) WHEN 0 THEN 0 ";
            $case_paid_holidays .= "WHEN {1} THEN 1 ";
            $case_paid_holidays .= "ELSE 0 ";
            $case_paid_holidays .= 'END ';

            $case_holiday_kubun = "CASE ifnull({0},0) WHEN 0 THEN 0 ";
            $case_holiday_kubun .= "WHEN {1} THEN 1 ";
            $case_holiday_kubun .= "WHEN {2} THEN 1 ";
            $case_holiday_kubun .= "WHEN {3} THEN 1 ";
            $case_holiday_kubun .= "WHEN {4} THEN 1 ";
            $case_holiday_kubun .= "WHEN {5} THEN 1 ";
            $case_holiday_kubun .= "WHEN {6} THEN 1 ";
            $case_holiday_kubun .= "WHEN {7} THEN 1 ";
            $case_holiday_kubun .= "WHEN {8} THEN 1 ";
            $case_holiday_kubun .= "WHEN {9} THEN 1 ";
            $case_holiday_kubun .= "WHEN {10} THEN 1 ";
            $case_holiday_kubun .= "WHEN {11} THEN 1 ";
            $case_holiday_kubun .= "WHEN {12} THEN 1 ";
            $case_holiday_kubun .= "WHEN {13} THEN 1 ";
            $case_holiday_kubun .= "ELSE 0 ";
            $case_holiday_kubun .= 'END ';

            $case_absence_kubun = "CASE ifnull({0},0) WHEN 0 THEN 0 ";
            $case_absence_kubun .= "WHEN {1} THEN 1 ";
            $case_absence_kubun .= "ELSE 0 ";
            $case_absence_kubun .= 'END ';

            $str_replace_working_status0 =str_replace('{0}', $this->table.'.working_status', $case_working_status);
            $str_replace_working_status1 =str_replace('{1}', Config::get('const.C012.attendance'), $str_replace_working_status0);
            $str_replace_working_status2 =str_replace('{2}', Config::get('const.C012.leaving'), $str_replace_working_status1);
            $str_replace_working_status3 =str_replace('{3}', Config::get('const.C012.missing_middle'), $str_replace_working_status2);
            $str_replace_working_status4 =str_replace('{4}', Config::get('const.C012.missing_middle_return'), $str_replace_working_status3);
            $str_replace_working_status5 =str_replace('{5}', Config::get('const.C012.public_going_out'), $str_replace_working_status4);
            $str_replace_working_status6 =str_replace('{6}', Config::get('const.C012.public_going_out_return'), $str_replace_working_status5);
            $str_replace_working_status7 =str_replace('{7}', Config::get('const.C012.continue_work'), $str_replace_working_status6);

            $str_replace_go_out0 =str_replace('{0}', $this->table.'.working_status', $case_go_out);
            $str_replace_go_out1 =str_replace('{1}', Config::get('const.C012.missing_middle'), $str_replace_go_out0);
            $str_replace_go_out2 =str_replace('{2}', Config::get('const.C012.public_going_out'), $str_replace_go_out1);

            $str_replace_paid_holidays0 =str_replace('{0}', $this->table.'.holiday_kubun', $case_paid_holidays);
            $str_replace_paid_holidays1 =str_replace('{1}', Config::get('const.C013.paid_holiday'), $str_replace_paid_holidays0);

            $str_replace_holiday_kubun0 =str_replace('{0}', $this->table.'.holiday_kubun', $case_holiday_kubun);
            $str_replace_holiday_kubun1 =str_replace('{1}', Config::get('const.C013.morning_off'), $str_replace_holiday_kubun0);
            $str_replace_holiday_kubun2 =str_replace('{2}', Config::get('const.C013.afternoon_off'), $str_replace_holiday_kubun1);
            $str_replace_holiday_kubun3 =str_replace('{3}', Config::get('const.C013.substitute_holiday'), $str_replace_holiday_kubun2);
            $str_replace_holiday_kubun4 =str_replace('{4}', Config::get('const.C013.compensation_holiday'), $str_replace_holiday_kubun3);
            $str_replace_holiday_kubun5 =str_replace('{5}', Config::get('const.C013.summer_leave'), $str_replace_holiday_kubun4);
            $str_replace_holiday_kubun6 =str_replace('{6}', Config::get('const.C013.year_end_and_new_year_leave'), $str_replace_holiday_kubun5);
            $str_replace_holiday_kubun7 =str_replace('{7}', Config::get('const.C013.organization_anniversary'), $str_replace_holiday_kubun6);
            $str_replace_holiday_kubun8 =str_replace('{8}', Config::get('const.C013.prenatal_postnatal'), $str_replace_holiday_kubun7);
            $str_replace_holiday_kubun9 =str_replace('{9}', Config::get('const.C013.physiology_days_leave'), $str_replace_holiday_kubun8);
            $str_replace_holiday_kubun10 =str_replace('{10}', Config::get('const.C013.childcare_care_leave'), $str_replace_holiday_kubun9);
            $str_replace_holiday_kubun11 =str_replace('{11}', Config::get('const.C013.nursing_care_leave'), $str_replace_holiday_kubun10);
            $str_replace_holiday_kubun12 =str_replace('{12}', Config::get('const.C013.congratulatory_or_consolatory_leave'), $str_replace_holiday_kubun11);
            $str_replace_holiday_kubun13 =str_replace('{13}', Config::get('const.C013.refresh_leave'), $str_replace_holiday_kubun12);

            $str_replace_leave_early_kubun0 =str_replace('{0}', $this->table.'.holiday_kubun', $case_absence_kubun);
            $str_replace_leave_early_kubun1 =str_replace('{1}', Config::get('const.C013.leave_early_work'), $str_replace_leave_early_kubun0);

            $str_replace_late_kubun0 =str_replace('{0}', $this->table.'.holiday_kubun', $case_absence_kubun);
            $str_replace_late_kubun1 =str_replace('{1}', Config::get('const.C013.late_work'), $str_replace_late_kubun0);

            $str_replace_absence_kubun0 =str_replace('{0}', $this->table.'.holiday_kubun', $case_absence_kubun);
            $str_replace_absence_kubun1 =str_replace('{1}', Config::get('const.C013.absence_work'), $str_replace_absence_kubun0);

            $subquery = DB::table($this->table)
                ->selectRaw('sum(ifnull('.$this->table.'.total_working_times, 0)) as total_working_times')
                ->selectRaw('sum(ifnull('.$this->table.'.regular_working_times, 0)) as regular_working_times')
                ->selectRaw('sum(ifnull('.$this->table.'.out_of_regular_working_times, 0)) as out_of_regular_working_times')
                ->selectRaw('sum(ifnull('.$this->table.'.overtime_hours, 0)) as overtime_hours')
                ->selectRaw('sum(ifnull('.$this->table.'.late_night_overtime_hours, 0)) as late_night_overtime_hours')
                ->selectRaw('sum(ifnull('.$this->table.'.late_night_working_hours, 0)) as late_night_working_hours')
                ->selectRaw('sum(ifnull('.$this->table.'.legal_working_times, 0)) as legal_working_times')
                ->selectRaw('sum(ifnull('.$this->table.'.out_of_legal_working_times, 0)) as out_of_legal_working_times')
                ->selectRaw('sum(ifnull('.$this->table.'.not_employment_working_hours, 0)) as not_employment_working_hours')
                ->selectRaw('sum(ifnull('.$this->table.'.off_hours_working_hours, 0)) as off_hours_working_hours')
                ->selectRaw('sum(ifnull('.$this->table.'.public_going_out_hours, 0)) as public_going_out_hours')
                ->selectRaw('sum(ifnull('.$this->table.'.missing_middle_hours, 0)) as missing_middle_hours')
                ->selectRaw('sum(ifnull('.$this->table.'.out_of_legal_working_holiday_hours, 0)) as out_of_legal_working_holiday_hours')
                ->selectRaw('sum(ifnull('.$this->table.'.legal_working_holiday_hours, 0)) as legal_working_holiday_hours')
                ->selectRaw('sum('.$str_replace_working_status7.') as total_working_status')
                ->selectRaw('sum('.$str_replace_go_out2.') as total_go_out')
                ->selectRaw('sum('.$str_replace_paid_holidays1.') as total_paid_holidays')
                ->selectRaw('sum('.$str_replace_holiday_kubun13.') as total_holiday_kubun')
                ->selectRaw('sum('.$str_replace_leave_early_kubun1.') as total_leave_early')
                ->selectRaw('sum('.$str_replace_late_kubun1.') as total_late')
                ->selectRaw('sum('.$str_replace_absence_kubun1.') as total_absence');
            if ($dayormonth == Config::get('const.WORKINGTIME_DAY_OR_MONTH.daily_basic')) {
                $subquery->addselect($this->table.'.working_date');
            }

            $array_groupby = [];
            $subquery = $this->setWhereSql($subquery);
            
            $case_where_1 = "";
            $case_where_working_time_name = "";
            $case_where_predeter_time_name = "";
            $case_where_predeter_time_secondname = "";
            $case_where_predeter_night_time_name = "";
            $case_where_predeter_night_time_secondname = "";
            if ($dayormonth == Config::get('const.WORKINGTIME_DAY_OR_MONTH.daily_basic')) {
                $array_groupby[] = $this->table.'.working_date';

                $case_where_1 = "CASE ifnull({0},{1}) WHEN {1} THEN '{2}' ";
                $case_where_1 .= " WHEN {3} THEN '{4}' ";
                $case_where_1 .= " WHEN {5} THEN '{6}' ";
                $case_where_1 .= " ELSE '{2}' ";
                $case_where_1 .= ' END as {7}';
    
                $case_where_working_time_name = str_replace('{0}', 't21.business_kubun', $case_where_1);
                $case_where_working_time_name = str_replace('{1}', Config::get('const.C007.basic'), $case_where_working_time_name);
                $case_where_working_time_name = str_replace('{2}', Config::get('const.WORKING_TIME_NAME.basic'), $case_where_working_time_name);
                $case_where_working_time_name = str_replace('{3}', Config::get('const.C007.legal_holoday'), $case_where_working_time_name);
                $case_where_working_time_name = str_replace('{4}', Config::get('const.WORKING_TIME_NAME.legal_holoday'), $case_where_working_time_name);
                $case_where_working_time_name = str_replace('{5}', Config::get('const.C007.legal_out_holoday'), $case_where_working_time_name);
                $case_where_working_time_name = str_replace('{6}', Config::get('const.WORKING_TIME_NAME.legal_out_holoday'), $case_where_working_time_name);
                $case_where_working_time_name = str_replace('{7}', 'working_time_name', $case_where_working_time_name);
    
                $case_where_predeter_time_name = str_replace('{0}', 't21.business_kubun', $case_where_1);
                $case_where_predeter_time_name = str_replace('{1}', Config::get('const.C007.basic'), $case_where_predeter_time_name);
                $case_where_predeter_time_name = str_replace('{2}', Config::get('const.PREDETER_TIME_NAME.basic'), $case_where_predeter_time_name);
                $case_where_predeter_time_name = str_replace('{3}', Config::get('const.C007.legal_holoday'), $case_where_predeter_time_name);
                $case_where_predeter_time_name = str_replace('{4}', Config::get('const.PREDETER_TIME_NAME.legal_holoday'), $case_where_predeter_time_name);
                $case_where_predeter_time_name = str_replace('{5}', Config::get('const.C007.legal_out_holoday'), $case_where_predeter_time_name);
                $case_where_predeter_time_name = str_replace('{6}', Config::get('const.PREDETER_TIME_NAME.legal_out_holoday'), $case_where_predeter_time_name);
                $case_where_predeter_time_name = str_replace('{7}', 'predeter_time_name', $case_where_predeter_time_name);
    
                $case_where_predeter_time_secoundname = str_replace('{0}', 't21.business_kubun', $case_where_1);
                $case_where_predeter_time_secoundname = str_replace('{1}', Config::get('const.C007.basic'), $case_where_predeter_time_secoundname);
                $case_where_predeter_time_secoundname = str_replace('{2}', Config::get('const.PREDETER_TIME_SECONDNAME.basic'), $case_where_predeter_time_secoundname);
                $case_where_predeter_time_secoundname = str_replace('{3}', Config::get('const.C007.legal_holoday'), $case_where_predeter_time_secoundname);
                $case_where_predeter_time_secoundname = str_replace('{4}', Config::get('const.PREDETER_TIME_SECONDNAME.legal_holoday'), $case_where_predeter_time_secoundname);
                $case_where_predeter_time_secoundname = str_replace('{5}', Config::get('const.C007.legal_out_holoday'), $case_where_predeter_time_secoundname);
                $case_where_predeter_time_secoundname = str_replace('{6}', Config::get('const.PREDETER_TIME_SECONDNAME.legal_out_holoday'), $case_where_predeter_time_secoundname);
                $case_where_predeter_time_secoundname = str_replace('{7}', 'predeter_time_secoundname', $case_where_predeter_time_secoundname);
    
                $case_where_predeter_night_time_name = str_replace('{0}', 't21.business_kubun', $case_where_1);
                $case_where_predeter_night_time_name = str_replace('{1}', Config::get('const.C007.basic'), $case_where_predeter_night_time_name);
                $case_where_predeter_night_time_name = str_replace('{2}', Config::get('const.PREDETER_NIGHT_TIME_NAME.basic'), $case_where_predeter_night_time_name);
                $case_where_predeter_night_time_name = str_replace('{3}', Config::get('const.C007.legal_holoday'), $case_where_predeter_night_time_name);
                $case_where_predeter_night_time_name = str_replace('{4}', Config::get('const.PREDETER_NIGHT_TIME_NAME.legal_holoday'), $case_where_predeter_night_time_name);
                $case_where_predeter_night_time_name = str_replace('{5}', Config::get('const.C007.legal_out_holoday'), $case_where_predeter_night_time_name);
                $case_where_predeter_night_time_name = str_replace('{6}', Config::get('const.PREDETER_NIGHT_TIME_NAME.legal_out_holoday'), $case_where_predeter_night_time_name);
                $case_where_predeter_night_time_name = str_replace('{7}', 'predeter_night_time_name', $case_where_predeter_night_time_name);
    
                $case_where_predeter_night_time_secoundname = str_replace('{0}', 't21.business_kubun', $case_where_1);
                $case_where_predeter_night_time_secoundname = str_replace('{1}', Config::get('const.C007.basic'), $case_where_predeter_night_time_secoundname);
                $case_where_predeter_night_time_secoundname = str_replace('{2}', Config::get('const.PREDETER_NIGHT_TIME_SECONDNAME.basic'), $case_where_predeter_night_time_secoundname);
                $case_where_predeter_night_time_secoundname = str_replace('{3}', Config::get('const.C007.legal_holoday'), $case_where_predeter_night_time_secoundname);
                $case_where_predeter_night_time_secoundname = str_replace('{4}', Config::get('const.PREDETER_NIGHT_TIME_SECONDNAME.legal_holoday'), $case_where_predeter_night_time_secoundname);
                $case_where_predeter_night_time_secoundname = str_replace('{5}', Config::get('const.C007.legal_out_holoday'), $case_where_predeter_night_time_secoundname);
                $case_where_predeter_night_time_secoundname = str_replace('{6}', Config::get('const.PREDETER_NIGHT_TIME_SECONDNAME.legal_out_holoday'), $case_where_predeter_night_time_secoundname);
                $case_where_predeter_night_time_secoundname = str_replace('{7}', 'predeter_night_time_secoundname', $case_where_predeter_night_time_secoundname);
            }
            if(!empty($this->param_employment_status)){
                $array_groupby[] = $this->table.'.employment_status';
            }
            if(!empty($this->param_user_code)){
                $array_groupby[] = $this->table.'.user_code';
            }
            if(!empty($this->param_department_code)){
                $array_groupby[] = $this->table.'.department_code';
            }
            if (count($array_groupby) > 0) {
                $subquery->groupby($array_groupby);
            }
            $subquery1 = $subquery->toSql();

            $mainquery = DB::table(DB::raw('('.$subquery1.') AS t1'))
                ->selectRaw(str_replace('{1}', 'total_working_times', str_replace('{0}', 't1.total_working_times', $case_where)))
                ->selectRaw(str_replace('{1}', 'regular_working_times', str_replace('{0}', 't1.regular_working_times', $case_where)))
                ->selectRaw(str_replace('{1}', 'out_of_regular_working_times', str_replace('{0}', 't1.out_of_regular_working_times', $case_where)))
                ->selectRaw(str_replace('{1}', 'overtime_hours', str_replace('{0}', 't1.overtime_hours', $case_where)))
                ->selectRaw(str_replace('{1}', 'late_night_overtime_hours', str_replace('{0}', 't1.late_night_overtime_hours', $case_where)))
                ->selectRaw(str_replace('{1}', 'late_night_working_hours', str_replace('{0}', 't1.late_night_working_hours', $case_where)))
                ->selectRaw(str_replace('{1}', 'legal_working_times', str_replace('{0}', 't1.legal_working_times', $case_where)))
                ->selectRaw(str_replace('{1}', 'out_of_legal_working_times', str_replace('{0}', 't1.out_of_legal_working_times', $case_where)))
                ->selectRaw(str_replace('{1}', 'not_employment_working_hours', str_replace('{0}', 't1.not_employment_working_hours', $case_where)))
                ->selectRaw(str_replace('{1}', 'off_hours_working_hours', str_replace('{0}', 't1.off_hours_working_hours', $case_where)))
                ->selectRaw(str_replace('{1}', 'public_going_out_hours', str_replace('{0}', 't1.public_going_out_hours', $case_where)))
                ->selectRaw(str_replace('{1}', 'missing_middle_hours', str_replace('{0}', 't1.missing_middle_hours', $case_where)))
                ->selectRaw(str_replace('{1}', 'out_of_legal_working_holiday_hours', str_replace('{0}', 't1.out_of_legal_working_holiday_hours', $case_where)))
                ->selectRaw(str_replace('{1}', 'legal_working_holiday_hours', str_replace('{0}', 't1.legal_working_holiday_hours', $case_where)))
                ->selectRaw('ifnull(t1.total_working_status, 0) as total_working_status' )
                ->selectRaw('ifnull(t1.total_go_out, 0) as total_go_out' )
                ->selectRaw('ifnull(t1.total_paid_holidays, 0) as total_paid_holidays' )
                ->selectRaw('ifnull(t1.total_holiday_kubun, 0) as total_holiday_kubun' )
                ->selectRaw('ifnull(t1.total_leave_early, 0) as total_leave_early' )
                ->selectRaw('ifnull(t1.total_late, 0) as total_late' )
                ->selectRaw('ifnull(t1.total_absence, 0) as total_absence' );
            if ($dayormonth == Config::get('const.WORKINGTIME_DAY_OR_MONTH.daily_basic')) {
                $mainquery
                    ->selectRaw($case_where_working_time_name)
                    ->selectRaw($case_where_predeter_time_name)
                    ->selectRaw($case_where_predeter_time_secoundname)
                    ->selectRaw($case_where_predeter_night_time_name)
                    ->selectRaw($case_where_predeter_night_time_secoundname);

                $subquery2 = DB::table($this->table_calendars)
                    ->select(
                        $this->table_calendars.'.date',
                        $this->table_calendars.'.business_kubun'
                    )
                    ->groupBy($this->table_calendars.'.date', $this->table_calendars.'.business_kubun');
                $mainquery->leftJoinSub($subquery2, 't21', function ($join) { 
                    $join->on('t21.date', '=', 't1.working_date' );
                });
            }
                
            $array_setBindingsStr = array();
            $cnt = 0;
            if(!empty($this->param_date_from) && !empty($this->param_date_to)){
                $cnt += 1;
                $array_setBindingsStr[] = array($cnt=>$this->param_date_from);
                $cnt += 1;
                $array_setBindingsStr[] = array($cnt=>$this->param_date_to);
            }
            
            if(!empty($this->param_employment_status)){
                $cnt += 1;
                $array_setBindingsStr[] = array($cnt=>$this->param_employment_status);
            }
            
            if(!empty($this->param_user_code)){
                $cnt += 1;
                $array_setBindingsStr[] = array($cnt=>$this->param_user_code);
            }
            
            if(!empty($this->param_department_code)){
                $cnt += 1;
                $array_setBindingsStr[] = array($cnt=>$this->param_department_code);
            }

            if (count($array_setBindingsStr) > 0) {
                $mainquery->setBindings($array_setBindingsStr);
            }

            $result = $mainquery->get();
                
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
     * 累計時間取得
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
    public function getMonthlyAlertTimeSum($targetdate){


        // 日時労働時間合計取得SQL作成
        try{
            // 各月の集計SQL
            $array_subquery = array(); 
            for ($i=0;$i<count($this->array_param_date_from);$i++) {
                $array_subquery[] = DB::table($this->table)
                    ->select(
                        $this->table.'.employment_status as employment_status',
                        $this->table.'.department_code as department_code',
                        $this->table.'.user_code as user_code')
                    ->selectRaw('DATE_FORMAT(MAX('.$this->table.".working_date), '%Y年%m月') as working_date")
                    ->selectRaw('SUM(IFNULL('.$this->table.'.overtime_hours, 0) + IFNULL('.$this->table.'.late_night_overtime_hours, 0)) as total_working_times')
                    ->selectRaw(
                        'SUM(IFNULL('.$this->table.'.overtime_hours, 0) + IFNULL('.$this->table.'.late_night_overtime_hours, 0)
                            - IFNULL('.$this->table.'.legal_working_holiday_hours, 0) - IFNULL('.$this->table.'.out_of_legal_working_holiday_hours, 0)) as total_noholiday_working_times'
                        )
                    ->where($this->table.'.working_date', '>=', $this->array_param_date_from[$i])
                    ->where($this->table.'.working_date', '<=', $this->array_param_date_to[$i])
                    ->where($this->table.'.is_deleted', '=', 0)
                    ->groupBy($this->table.'.employment_status', $this->table.'.department_code', $this->table.'.user_code');
            }

            // 適用期間日付の取得
            $apicommon = new ApiCommonController();
            // usersの最大適用開始日付subquery
            $subquery1 = $apicommon->getUserApplyTermSubquery($targetdate);
            // departmentsの最大適用開始日付subquery
            $subquery2 = $apicommon->getDepartmentApplyTermSubquery($targetdate);

            $mainquery = DB::table(DB::raw($this->table_users.' AS t1'))
                ->select(
                    't22.code_name as employment_status_name',
                    't21.name as department_name',
                    't1.name as user_name',
                    't1.code as user_code'
                );
            for ($i=0;$i<count($this->array_param_date_from);$i++) {
                $this->as_number = $i+1;
                $this->arias_number = $i+2;
                $mainquery
                    ->addselect('t'.$this->arias_number.'.working_date as working_date_'.$this->as_number);
                $mainquery
                    ->selectRaw('IFNULL(t'.$this->arias_number.'.total_working_times, 0) as total_working_times_'.$this->as_number)
                    ->selectRaw('IFNULL(t'.$this->arias_number.'.total_noholiday_working_times, 0) as total_noholiday_working_times_'.$this->as_number);
            }
            for ($i=0;$i<count($this->array_param_date_from);$i++) {
                $this->arias_number = $i+2;
                $mainquery
                    ->leftJoinSub($array_subquery[$i], 't'.$this->arias_number, function ($join) { 
                        $join->on('t'.$this->arias_number.'.employment_status', '=', 't1.employment_status');
                        $join->on('t'.$this->arias_number.'.department_code', '=', 't1.department_code');
                        $join->on('t'.$this->arias_number.'.user_code', '=', 't1.code');
                    });
            }

            $mainquery
                ->leftJoinSub($subquery2, 't21', function ($join) { 
                    $join->on('t21.code', '=', 't1.department_code')
                    ->where('t1.is_deleted', '=', 0);
                })
                ->leftJoin('generalcodes as t22', function ($join) { 
                    $join->on('t22.code', '=', 't1.employment_status')
                    ->where('t22.identification_id', '=', Config::get('const.C001.value'))
                    ->where('t1.is_deleted', '=', 0)
                    ->where('t22.is_deleted', '=', 0);
                });
    
            $mainquery
                ->JoinSub($subquery1, 't23', function ($join) { 
                    $join->on('t23.code', '=', 't1.code');
                    $join->on('t23.max_apply_term_from', '=', 't1.apply_term_from');
                });

            if(!empty($this->param_employment_status)){
                $mainquery->where('t1.employment_status', $this->param_employment_status);   //employment_status指定
            }
            
            if(!empty($this->param_user_code)){
                $mainquery->where('t1.code', $this->param_user_code);                   //user_code指定
            }
            
            if(!empty($this->param_department_code)){
                $mainquery->where('t1.department_code', $this->param_department_code);       //department_code指定
            }

            $result = $mainquery
                ->where('t1.role', '<', Config::get('const.C017.out_of_user'))
                ->where('t1.is_deleted', '=', 0)
                ->orderBy('t1.department_code', 'asc')
                ->orderBy('t1.employment_status', 'asc')
                ->orderBy('t1.code', 'asc')
                ->get();
                
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
    public function insertWorkingTimeDateFromTemp($temp_working_time_dates){
        try{
            $item_data = '';
            $temp_array = array();
            foreach($temp_working_time_dates as $working_time_date) {
                $temp_collect = collect($working_time_date);
                for ($i=1;$i<=5;$i++) {
                    if (isset($temp_collect['attendance_time_positions_'.$i]) && $temp_collect['attendance_time_positions_'.$i] != "") {
                        $item_data = $temp_collect['attendance_time_positions_'.$i];
                        $temp_collect['attendance_time_positions_'.$i] = DB::raw("(GeomFromText('POINT(".$item_data.")'))");
                    }
                }
                for ($i=1;$i<=5;$i++) {
                    if (isset($temp_collect['leaving_time_positions_'.$i]) && $temp_collect['leaving_time_positions_'.$i] != "") {
                        $item_data = $temp_collect['leaving_time_positions_'.$i];
                        $temp_collect['leaving_time_positions_'.$i] = DB::raw("(GeomFromText('POINT(".$item_data.")'))");
                    }
                }
                for ($i=1;$i<=5;$i++) {
                    if (isset($temp_collect['missing_middle_time_positions_'.$i]) && $temp_collect['missing_middle_time_positions_'.$i] != "") {
                       $item_data = $temp_collect['missing_middle_time_positions_'.$i];
                        $temp_collect['missing_middle_time_positions_'.$i] = DB::raw("(GeomFromText('POINT(".$item_data.")'))");
                    }
                }
                for ($i=1;$i<=5;$i++) {
                    if (isset($temp_collect['missing_middle_return_time_positions_'.$i]) && $temp_collect['missing_middle_return_time_positions_'.$i] != "") {
                        $item_data = $temp_collect['missing_middle_return_time_positions_'.$i];
                        $temp_collect['missing_middle_return_time_positions_'.$i] = DB::raw("(GeomFromText('POINT(".$item_data.")'))");
                    }
                }
                for ($i=1;$i<=5;$i++) {
                    if (isset($temp_collect['public_going_out_time_positions_'.$i]) && $temp_collect['public_going_out_time_positions_'.$i] != "") {
                        $item_data = $temp_collect['public_going_out_time_positions_'.$i];
                        $temp_collect['public_going_out_time_positions_'.$i] = DB::raw("(GeomFromText('POINT(".$item_data.")'))");
                    }
                }
                for ($i=1;$i<=5;$i++) {
                    if (isset($temp_collect['public_going_out_return_time_positions_'.$i]) && $temp_collect['public_going_out_return_time_positions_'.$i] != "") {
                        $item_data = $temp_collect['public_going_out_return_time_positions_'.$i];
                        $temp_collect['public_going_out_return_time_positions_'.$i] = DB::raw("(GeomFromText('POINT(".$item_data.")'))");
                    }
                }
                $temp_array[] = $temp_collect->toArray();
            } 
            DB::table($this->table)->insert($temp_array);
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
     * 存在チェック
     *
     * @return boolean
     */
    public function isExistsWorkingTimeDate(){
        // if (Config::get('const.DEBUG_LEVEL') == Config::get('const.DEBUG_LEVEL_VALUE.DEBUG')) { \DB::enableQueryLog(); }
        try{
            $mainquery = DB::table($this->table);

            $mainquery = $this->setWhereSql($mainquery);

            $result = $mainquery->exists();
            // if (Config::get('const.DEBUG_LEVEL') == Config::get('const.DEBUG_LEVEL_VALUE.DEBUG')) {
            //     \Log::debug('sql_debug_log', ['isExistsWorkingTimeDate' => \DB::getQueryLog()]);
            //     \DB::disableQueryLog();
            // }
            return $result;

        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_exists_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_exists_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

    }

    /**
     * 削除
     *
     * @return void
     */
    public function delWorkingTimeDate(){
        try{
            // if (Config::get('const.DEBUG_LEVEL') == Config::get('const.DEBUG_LEVEL_VALUE.DEBUG')) { \DB::enableQueryLog(); }
            $mainquery = DB::table($this->table);

            $mainquery = $this->setWhereSql($mainquery);
            
            $mainquery->delete();
            // if (Config::get('const.DEBUG_LEVEL') == Config::get('const.DEBUG_LEVEL_VALUE.DEBUG')) {
            //     \Log::debug('sql_debug_log', ['delWorkingTimeDate' => \DB::getQueryLog()]);
            //     \DB::disableQueryLog();
            // }

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
    
    /**
     * 条件設定（$this->tableのみ）
     *
     * @return query
     */
    public function setWhereSql($query){
        try{

            if(!empty($this->param_date_from) && !empty($this->param_date_to)){
                $date = date_create($this->param_date_from);
                $this->param_date_from = $date->format('Ymd');
                $date = date_create($this->param_date_to);
                $this->param_date_to = $date->format('Ymd');
                $query->where($this->table.'.working_date', '>=', $this->param_date_from);          // 日付範囲指定
                $query->where($this->table.'.working_date', '<=', $this->param_date_to);            // 日付範囲指定
            }
            
            if(!empty($this->param_employment_status)){
                $query->where($this->table.'.employment_status', $this->param_employment_status);   //employment_status指定
            }
            
            if(!empty($this->param_user_code)){
                $query->where($this->table.'.user_code', $this->param_user_code);                   //user_code指定
            }
            
            if(!empty($this->param_department_code)){
                $query->where($this->table.'.department_code', $this->param_department_code);       //department_code指定
            }

        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.where_illegal')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }


        return $query;

    }
    
    /**
     * 条件設定（$this->tableusers）
     *
     * @return query
     */
    public function setWhereSqlUsers($query){
        try{

            if(!empty($this->param_date_from) && !empty($this->param_date_to)){
                $date = date_create($this->param_date_from);
                $this->param_date_from = $date->format('Ymd');
                $date = date_create($this->param_date_to);
                $this->param_date_to = $date->format('Ymd');
                $query->where($this->table_calendars.'.date', '>=', $this->param_date_from);            // 日付範囲指定
                $query->where($this->table_calendars.'.date', '<=', $this->param_date_to);              // 日付範囲指定
            }
            
            if(!empty($this->param_employment_status)){
                $query->where($this->table_users.'.employment_status', $this->param_employment_status);         //employment_status指定
            }
            
            if(!empty($this->param_user_code)){
                $query->where($this->table_users.'.code', $this->param_user_code);                      //user_code指定
            }
            
            if(!empty($this->param_department_code)){
                $query->where($this->table_users.'.department_code', $this->param_department_code);     //department_code指定
            }

        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.where_illegal')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }


        return $query;

    }

}
