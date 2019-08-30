<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Calendar extends Model
{
    protected $table = 'calendars';
 
    private $date;                  
    private $weekday_kubun;                  
    private $business_kubun;       
    private $holiday_kubun;     
    private $created_user;    
    private $updated_user;                  
    private $created_at;                  
    private $updated_at;                  
    private $is_deleted;                  
   

    public function getDateAttribute()
    {
        return $this->date;
    }

    public function setDateAttribute($value)
    {
        $this->date = $value;
    }

    public function getWeekdaykubunAttribute()
    {
        return $this->weekday_kubun;
    }

    public function setWeekdaykubunAttribute($value)
    {
        $this->weekday_kubun = $value;
    }

    public function getBusinesskubunAttribute()
    {
        return $this->business_kubun;
    }

    public function setBusinesskubunAttribute($value)
    {
        $this->business_kubun = $value;
    }
     
    public function getHolidaykubunAttribute()
    {
        return $this->holiday_kubun;
    }

    public function setHolidaykubunAttribute($value)
    {
        $this->holiday_kubun = $value;
    }

    public function getCreateduserAttribute()
    {
        return $this->created_user;
    }

    public function setCreateduserAttribute($value)
    {
        $this->created_user = $value;
    }
  
    public function getUpdateduserAttribute()
    {
        return $this->updated_user;
    }

    public function setUpdateduserAttribute($value)
    {
        $this->updated_user = $value;
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
     
    public function getIsdeletedAttribute()
    {
        return $this->is_deleted;
    }

    public function setIsdeletedAttribute($value)
    {
        $this->is_deleted = $value;
    }

    /**
     * 登録(INSERT)
     *
     * @return void
     */
    public function insert(){
        DB::table($this->table)->insert(
            [
                'date' => $this->date,
                'weekday_kubun' => $this->weekday_kubun,
                'business_kubun' => $this->business_kubun,
                'holiday_kubun' => $this->holiday_kubun,
                'created_user' => $this->created_user,
                'created_at' => $this->created_at,
            ]
        );
    }

    public function updateCalendar(){
        DB::table($this->table)
            ->where('date',$this->date)
            ->where('is_deleted', 0)
            ->update([
                'business_kubun' => $this->business_kubun,
                'holiday_kubun' => $this->holiday_kubun,
                'updated_user' => $this->updated_user,
                'updated_at' => $this->updated_at
                ]
            );
    }

    /**
     * date存在チェック
     *
     * @return boolean
     */
    public function isExistsDate(){
        $is_exists = DB::table($this->table)
            ->where('date',$this->date)
            ->where('is_deleted',0)
            ->exists();

        return $is_exists;
    }

    /**
     * date削除
     *
     * @return void
     */
    public function delDate(){
        DB::table($this->table)
            ->where('date', $this->date)
            ->delete();
    }

    /**
     * 検索
     *
     * @return void
     */
    public function getDetail(){
        $data = DB::table($this->table)
        ->select(
                'date',
                'weekday_kubun',
                'business_kubun',
                'holiday_kubun'
        );
        if(isset($this->date)){
            $data->where('date','LIKE','%'.$this->date.'%');
        }
        if(isset($this->business_kubun)){
            $data->where('business_kubun',$this->business_kubun);
        }
        if(isset($this->holiday_kubun)){
            $data->where('holiday_kubun',$this->holiday_kubun);
        }
        $data->where('is_deleted',0);
        $result = $data->get();


        return $result;
    }

    /**
     * 検索
     *
     * @return void
     */
    public function getCalenderDate(){
        $data = DB::table($this->table)
        ->select(
                'date',
                'weekday_kubun',
                'business_kubun',
                'holiday_kubun'
        );
        if(isset($this->date)){
            $data->where('date', $this->date);
        }
        if(isset($this->business_kubun)){
            $data->where('business_kubun',$this->business_kubun);
        }
        if(isset($this->holiday_kubun)){
            $data->where('holiday_kubun',$this->holiday_kubun);
        }
        $data->where('is_deleted',0);
        $result = $data->get();


        return $result;
    }

}
