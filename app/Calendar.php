<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class Calendar extends Model
{
    protected $table = 'calendars';
    protected $table_public_holidays = 'public_holidays';
 
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
            $this->table.'.date',
            $this->table.'.weekday_kubun',
            $this->table.'.business_kubun',
            $this->table.'.holiday_kubun'
        )
        ->selectRaw(
            "concat(
                DATE_FORMAT(".$this->table.".date,'%Y年%m月%d日'),'(',substring('月火水木金土日',convert(".$this->table.".weekday_kubun+1,char),1),') '
            , ifnull(".$this->table_public_holidays.".name, '')) as date_name ");
        $data->leftJoin($this->table_public_holidays, function ($join) { 
            $join->on($this->table_public_holidays.'.date', '=', $this->table.'.date')
            ->where($this->table_public_holidays.'.is_deleted',0);
        });
    
        if(isset($this->date)){
            $data->where($this->table.'.date','LIKE','%'.$this->date.'%');
        }
        if(isset($this->business_kubun)){
            $data->where('business_kubun',$this->business_kubun);
        }
        if(isset($this->holiday_kubun)){
            $data->where('holiday_kubun',$this->holiday_kubun);
        }
        $data->where($this->table.'.is_deleted',0);
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

    /**
     * 1年分の検索
     *
     * @return void
     */
    public function getCalenderDateYear(){
        Log::debug('getCalenderDateYear in $date = '.$this->date);
        Log::debug('getCalenderDateYear in $created_user = '.$this->created_user);

        $result = true;
        \DB::enableQueryLog();

        $subquery1 = DB::table('information_schema.COLUMNS')
            ->selectRaw('@num := @num + 1');

        $subquery2 = DB::table($this->table)
            ->selectRaw('0 as generate_series')
            ->whereRaw("(@num := 1 - 1) * 0")
            ->unionAll($subquery1)
            ->limit(366)
            ->toSql();

        $subquery3 = DB::table(DB::raw('('.$subquery2.') AS t1'))
            ->selectRaw("date_format(date_add('".$this->date."', interval t1.generate_series day), '%Y%m%d') as dt")
            ->selectRaw("weekday(date_format(date_add('".$this->date."', interval t1.generate_series day), '%Y%m%d')) as we")
            ->toSql();
    
        $subquery4 = DB::table($this->table)
            ->selectRaw("date_format(date ('".$this->date."' + interval 1 year), '%Y%m%d') as dt")
            ->limit(1)
            ->toSql();
    
        $subquery5 = DB::table($this->table_public_holidays)
            ->selectRaw("date_format(".$this->table_public_holidays.".date, '%Y%m%d') as public_holidays_date")
            ->where($this->table_public_holidays.'.is_deleted', '=', 0);

        $case_sql = ' case ifnull(t3.public_holidays_date, 0) ';
        $case_sql .= ' when 0 then ';
        $case_sql .= '   case t2.we ';
        $case_sql .= '     when 5 then 3 ';
        $case_sql .= '     when 6 then 2 ';
        $case_sql .= '     else 1 ';
        $case_sql .= '   end ';
        $case_sql .= ' else 3 ';
        $case_sql .= ' end as business_kubun ';

        Log::debug("'".$this->created_user."' as created_user");
        $mainquery = DB::table(DB::raw('('.$subquery3.') AS t2'))
            ->select(
                't2.dt as date',
                't2.we as weekday_kubun'
            )
            ->selectRaw($case_sql)
            ->selectRaw('0 as holiday_kubun')
            ->selectRaw("'".$this->created_user."' as created_user")
            ->selectRaw('null as updated_user')
            ->selectRaw('now() as created_at')
            ->leftJoinSub($subquery5, 't3', function ($join) { 
                $join->on('t3.public_holidays_date', '=', 't2.dt');
            })
            ->where('t2.dt', '<=', DB::raw('('.$subquery4.')'))
            ->get();

        return $mainquery;
    }

    /**
     * 1年分の登録
     *
     * @return void
     */
    public function insCalenderDateYear($array_subquery){
        $result = true;

        try{
            DB::table($this->table)->insert($array_subquery);
        }catch(\PDOException $pe){
            Log::error(str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_insert_erorr')).'$pe');
            Log::error($pe->getMessage());
            $result = false;
            throw $pe;
        }catch(\Exception $e){
            Log::error(str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_insert_erorr')).'$e');
            Log::error($e->getMessage());
            $result = false;
            throw $e;
        }
        return $result;
    }

    /**
     * 1年分の削除
     *
     * @return void
     */
    public function delCalenderDateYear($fromdate, $todate){
        Log::debug('delCalenderDateYear in $fromdate = '.$fromdate);
        Log::debug('delCalenderDateYear in $todate = '.$todate);

        $result = true;

        try{
            DB::table($this->table)
            ->whereBetween('date', [$fromdate, $todate])
            ->delete();
        }catch(\PDOException $pe){
            Log::error(str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_delete_erorr')).'$pe');
            Log::error($pe->getMessage());
            $result = false;
            throw $pe;
        }catch(\Exception $e){
            Log::error(str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_delete_erorr')).'$e');
            Log::error($e->getMessage());
            $result = false;
            throw $e;
        }
        return $result;
    }

}
