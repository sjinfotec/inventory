<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;


class PaidHoliday extends Model
{
    protected $table_paid_holidays = 'paid_holidays';
    protected $table_users = 'users';

    private $user_code;                         // ユーザーコード
    private $year;                              // 年度
    private $type;                              // 単位
    private $paid_this_year;                    // 本年度付与日数
    private $paid_last_year;                    // 昨年度残日数
    private $paid_sum;                          // 付与日数
    private $created_user;                      // 作成ユーザー
    private $updated_user;                      // 修正ユーザー
    private $created_at;                        // 作成日時
    private $updated_at;                        // 修正日時

    //--------------- パラメータ項目属性 -----------------------------------

    private $param_department_code;                         // 部署コード
 

    // ユーザーコード
    public function getUserCodeAttribute()
    {
        return $this->user_code;
    }

    public function setUserCodeAttribute($value)
    {
        $this->user_code = $value;
    }
 
    // 年度
    public function getYearAttribute()
    {
        return $this->year;
    }

    public function setYearAttribute($value)
    {
        $this->year = $value;
    }

    // 単位
    public function getTypeAttribute()
    {
        return $this->type;
    }

    public function setTypeAttribute($value)
    {
        $this->type = $value;
    }

    // 本年度付与日数
    public function getPaidThisYearAttribute()
    {
        return $this->paid_this_year;
    }

    public function setPaidThisYearAttribute($value)
    {
        $this->paid_this_year = $value;
    }

    // 昨年度残日数
    public function getPaidLastYearAttribute()
    {
        return $this->paid_last_year;
    }

    public function setPaidLastYearAttribute($value)
    {
        $this->paid_last_year = $value;
    }

    // 付与日数
    public function getPaidSumAttribute()
    {
        return $this->paid_sum;
    }

    public function setPaidSumAttribute($value)
    {
        $this->paid_sum = $value;
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


    // 作成日時 
    public function getCreatedatAttribute()
    {
        return $this->created_at;
    }

    public function setCreatedatAttribute($value)
    {
        $this->created_at = $value;
    }


    // 修正日時 
    public function getUpdatedatAttribute()
    {
        return $this->updated_at;
    }

    public function setUpdatedatAttribute($value)
    {
        $this->updated_at = $value;
    }

     // 部署コード
    public function getParamdepartmentcodeAttribute()
    {
        return $this->param_department_code;
    }

    public function setParamdepartmentcodeAttribute($value)
    {
        $this->param_department_code = $value;
    }

    //--------------- メソッド -----------------------------------
    /**
     * 有給情報取得
     *
     * @return void
     */
    public function getPaidData(){
        $results = null;
        try {
            $sql = $this->getPaidDataSQL();
            // パラメータバインド
            $array_setBindingsStr = array();
            $array_setBindingsStr[] = $this->year;
            if(!empty($this->param_department_code)) {
                $array_setBindingsStr[] = $this->param_department_code;
            }
            if(!empty($this->user_code)) {
                $array_setBindingsStr[] = $this->user_code;
            }
            $results = DB::select($sql,$array_setBindingsStr);
            if(!isset($results)) { return null; }
            return $results;
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_paid_holidays, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_paid_holidays, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 有給情報更新
     *
     * @return void
     */
    public function updatePaidInformations(){
        try {
            DB::table($this->table_paid_holidays)
            ->where($this->table_paid_holidays.'.user_code', $this->user_code)
            ->where($this->table_paid_holidays.'.year', $this->year)
            ->where($this->table_paid_holidays.'.is_deleted', 0)
            ->update(
                [
                    'type' => $this->type,
                    'paid_this_year' => $this->paid_this_year,
                    'paid_last_year' => $this->paid_last_year,
                    'paid_sum' => $this->paid_sum,
                    'updated_user' => $this->updated_user,
                    'updated_at' => $this->updated_at,
                ]
            );
            return true;
    
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_update_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_update_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 有給登録
     *
     * @return void
     */
    public function insertPaidInformations(){
        try {
             DB::table($this->table_paid_holidays)->insert(
                [
                    'user_code' => $this->user_code, 
                    'year' => $this->year ,
                    'type' => $this->type ,
                    'paid_this_year' => $this->paid_this_year ,
                    'paid_last_year' => $this->paid_last_year ,
                    'paid_sum' => $this->paid_sum ,
                    'created_user' => $this->created_user ,
                    'created_at' => $this->created_at
                ]
            );
            return true;
    
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
     * 有給情報取得SQL
     *
     * @return void
     */
    public function getPaidDataSQL(){
        $sql = "";

        $sql = " select";
        $sql .= "  t3.code";
        $sql .= "  , t3.department_code";
        $sql .= "  , t3.name";
        $sql .= "  , t7.name as department_name";
        $sql .= "  , t4.year";
        $sql .= "  , t4.type";
        $sql .= "  , FORMAT(t4.paid_this_year,2) as paid_this_year";
        $sql .= "  , FORMAT(t4.paid_last_year,2) as paid_last_year";
        $sql .= "  , FORMAT(t4.paid_sum,2) as paid_sum";
        $sql .= "  , 0 as error_flag1";
        $sql .= "  , 0 as error_flag2";
        $sql .= "  , 0 as error_flag3";
        $sql .= "  , 0 as error_flag4";
        $sql .= " from";
        $sql .= "  ( ";
        $sql .= "    select";
        $sql .= "      t1.code";
        $sql .= "      , t1.department_code";
        $sql .= "      , t1.name";
        $sql .= "      , t1.apply_term_from";
        $sql .= "      , t1.is_deleted ";
        $sql .= "    from";
        $sql .= "      users t1 ";
        $sql .= "      inner join ( ";
        $sql .= "        select";
        $sql .= "          code";
        $sql .= "          , max(apply_term_from) as max ";
        $sql .= "        from";
        $sql .= "          users ";
        $sql .= "        group by";
        $sql .= "          code";
        $sql .= "      ) t2 ";
        $sql .= "        on t2.code = t1.code ";
        $sql .= "        and t2.max = t1.apply_term_from";
        $sql .= "  ) t3";
        $sql .= "  left join ";
        $sql .= "   ( ";
        $sql .= "    select";
        $sql .= "      t5.code";
        $sql .= "      , t5.name";
        $sql .= "      , t5.apply_term_from";
        $sql .= "    from";
        $sql .= "      departments t5 ";
        $sql .= "      inner join ( ";
        $sql .= "        select";
        $sql .= "          code";
        $sql .= "          , max(apply_term_from) as max2 ";
        $sql .= "        from";
        $sql .= "          departments ";
        $sql .= "        group by";
        $sql .= "          code";
        $sql .= "      ) t6 ";
        $sql .= "        on t6.code = t5.code ";
        $sql .= "        and t6.max2 = t5.apply_term_from";
        $sql .= "  ) t7 ";
        $sql .= "  on t7.code = t3.department_code";
        $sql .= "  left join ( ";
        $sql .= "    select";
        $sql .= "      user_code";
        $sql .= "      , year";
        $sql .= "      , type";
        $sql .= "      , paid_this_year";
        $sql .= "      , paid_last_year";
        $sql .= "      , paid_sum ";
        $sql .= "    from";
        $sql .= "      paid_holidays ";
        $sql .= "    where";
        $sql .= "      year = ?";
        $sql .= "  ) t4 ";
        $sql .= "    on t4.user_code = t3.code ";
        $sql .= "where";
        $sql .= "  1 = 1 ";
        $sql .= "  and t3.is_deleted = 0 ";
        if(!empty($this->param_department_code)) {
            $sql .= "   and t3.department_code = ? ";
        }
        if(!empty($this->user_code)) {
            $sql .= "   and t3.code = ? ";
        }
        $sql .= "   order by t3.department_code ,t3.code asc";

        return $sql;

    }
    
}
