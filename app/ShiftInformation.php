<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class ShiftInformation extends Model
{
    private $user_code;                  
    private $shift_satrt_time;                  
    private $shift_end_time;                  
    private $target_date;        
    private $start_target_date;        
    private $end_target_date;        

     
    public function getUsercodeAttribute()
    {
        return $this->user_code;
    }

    public function setUsercodeAttribute($value)
    {
        $this->user_code = $value;
    }
     
    public function getShiftsatrttimeAttribute()
    {
        return $this->shift_satrt_time;
    }

    public function setShiftsatrttimeAttribute($value)
    {
        $this->shift_satrt_time = $value;
    }
     
    public function getShiftendtimeAttribute()
    {
        return $this->shift_end_time;
    }

    public function setShiftendtimeAttribute($value)
    {
        $this->shift_end_time = $value;
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
            ->select('shift_informations.id', 
                    'shift_informations.shift_start_time',
                    'shift_informations.shift_end_time',
                    'shift_informations.target_date')
            ->where('users.code',$this->user_code)
            ->where('shift_informations.is_deleted', 0)
            ->whereBetween('shift_informations.target_date',[$start_date,$end_date])
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
                    'shift_start_time' => $this->shift_satrt_time,
                    'shift_end_time' => $this->shift_end_time,
                    'target_date' => $i
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
