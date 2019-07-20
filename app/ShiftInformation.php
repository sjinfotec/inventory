<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class ShiftInformation extends Model
{
    private $working_timetable_no; 
    private $user_code;                  
    private $target_date;        
    private $start_target_date;        
    private $end_target_date;
    private $created_at;                  
    private $updated_at;         
                     
     
    public function getWorkingtimetablenoAttribute()
    {
        return $this->working_timetable_no;
    }

    public function setWorkingtimetablenoAttribute($value)
    {
        $this->working_timetable_no = $value;
    }        
     
    public function getUsercodeAttribute()
    {
        return $this->user_code;
    }

    public function setUsercodeAttribute($value)
    {
        $this->user_code = $value;
    }
     
    public function getTargetdateAttribute()
    {
        return $this->target_date;
    }

    public function setTargetdateAttribute($value)
    {
        $this->target_date = $value;
    }
     
    public function getStarttargetdateAttribute()
    {
        return $this->start_target_date;
    }

    public function setStarttargetdateAttribute($value)
    {
        $this->start_target_date = $value;
    }

     
    public function getEndtargetdateAttribute()
    {
        return $this->end_target_date;
    }

    public function setEndtargetdateAttribute($value)
    {
        $this->end_target_date = $value;
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


    /**
     * ユーザーシフト取得
     *
     * @return void
     */
    public function getUserShift(){
        $today = Carbon::now();
        $start_date = $today->copy()->format("Y/m/d");
        $end_date = $today->copy()->addMonth(1)->format("Y/m/d");
        $shift_informatinos = DB::table('shift_informations')
            ->join('users', 'shift_informations.user_code', '=', 'users.code')
            ->join('working_timetables', 'shift_informations.working_timetable_no', '=', 'working_timetables.no')
            ->select( 
                    'shift_informations.id',
                    'shift_informations.working_timetable_no',
                    'working_timetables.name',
                    'shift_informations.target_date'
                    )
            ->where('users.code',$this->user_code)
            ->where('shift_informations.is_deleted', 0)
            ->whereBetween('shift_informations.target_date',[$start_date,$end_date])
            ->groupBy(
                    'shift_informations.id'
                    ,'shift_informations.working_timetable_no'
                    ,'working_timetables.name'
                    ,'shift_informations.target_date'
                    )
            ->orderBy('shift_informations.target_date','asc')
            ->get();

        return $shift_informatinos;
    }

    /**
     * シフト登録
     *
     * @return void
     */
    public function insertUserShift(){
        for ($i=$this->start_target_date; $i <= $this->end_target_date; $i++) { 
            DB::table('shift_informations')->insert(
                [
                    'user_code' => $this->user_code,
                    'working_timetable_no' => $this->working_timetable_no,
                    'target_date' => $i,
                    'created_at' => $this->created_at,
                ]
            );
        }
        
    }

    /**
     * シフト存在チェック
     *
     * @return boolean
     */
    public function isExistsShiftInfo(){
        $is_exists = DB::table('shift_informations')
            ->where('user_code',$this->user_code)
            ->whereBetween('target_date', [$this->start_target_date,$this->end_target_date])
            ->exists();

        return $is_exists;
    }

    /**
     * シフト削除
     *
     * @return void
     */
    public function delShiftInfo(){
        DB::table('shift_informations')
            ->where('user_code', $this->user_code)
            ->whereBetween('target_date', [$this->start_target_date,$this->end_target_date])
            ->delete();
    }

}
