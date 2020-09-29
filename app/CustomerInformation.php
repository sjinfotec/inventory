<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class CustomerInformation extends Model
{
    protected $table = 'customer_informations';
    protected $table_generalcodes = 'generalcodes';

    private $account_id;                        // アカウントID
    private $entry_type;                        // 問い合わせ種類
    private $entry_date;                        // 問い合わせ日付
    private $entry_time;                        // 問い合わせ時刻
    private $effective_from_date;               // 有効期限開始
    private $effective_to_date;                 // 有効期限終了
    private $company_name;                      // 会社名
    private $representative_name;               // 担当者氏名
    private $phone_number;                      // 電話番号
    private $email_value;                       // email
    private $post_code;                         // 郵便番号
    private $address_value;                     // 住所
    private $entry_contents;                    // entry_contents
    private $created_user;                      // 作成ユーザー
    private $updated_user;                      // 修正ユーザー
    private $created_at;                        // 作成日時
    private $updated_at;                        // 修正日時

    // アカウントID
    public function getAccountidAttribute()
    {
        return $this->account_id;
    }

    public function setAccountidAttribute($value)
    {
        $this->account_id = $value;
    }

    // 問い合わせ種類
    public function getEntryTypeAttribute()
    {
        return $this->entry_type;
    }

    public function stEntryTypeAttribute($value)
    {
        $this->entry_type = $value;
    }

         
    // 問い合わせ日付
    public function getEntryDateAttribute()
    {
        return $this->entry_date;
    }

    public function setEntryDateAttribute($value)
    {
        $this->entry_date = $value;
    }


    // 問い合わせ時刻 
    public function getEntryTimeAttribute()
    {
        return $this->entry_time;
    }

    public function setEntryTimeAttribute($value)
    {
        $this->entry_time = $value;
    }


    // 有効期限開始 
    public function getEffectiveFromDateAttribute()
    {
        return $this->effective_from_date;
    }

    public function setEffectiveFromDateAttribute($value)
    {
        $this->effective_from_date = $value;
    }


    // 有効期限終了 
    public function getEeffectiveToDateAttribute()
    {
        return $this->effective_to_date;
    }

    public function setEeffectiveToDateAttribute($value)
    {
        $this->effective_to_date = $value;
    }


    // 会社名 
    public function getCompanyNameAttribute()
    {
        return $this->company_name;
    }

    public function setCompanyNameAttribute($value)
    {
        $this->company_name = $value;
    }


    // 担当者氏名 
    public function getRepresentativeNameAttribute()
    {
        return $this->representative_name;
    }

    public function setRepresentativeNameAttribute($value)
    {
        $this->representative_name = $value;
    }


    // 電話番号 
    public function getPhoneNumberAttribute()
    {
        return $this->phone_number;
    }

    public function setPhoneNumberAttribute($value)
    {
        $this->phone_number = $value;
    }


    // email 
    public function getEmailValueAttribute()
    {
        return $this->email_value;
    }

    public function setEmailValueAttribute($value)
    {
        $this->email_value = $value;
    }


    // 郵便番号 
    public function getPostCodeAttribute()
    {
        return $this->post_code;
    }

    public function setPostCodeAttribute($value)
    {
        $this->post_code = $value;
    }


    // 住所 
    public function getAddressValueAttribute()
    {
        return $this->address_value;
    }

    public function setAddressValueAttribute($value)
    {
        $this->address_value = $value;
    }


    // 問い合わせ内容
    public function getEntryContentsAttribute()
    {
        return $this->entry_contents;
    }

    public function setEntryContentsAttribute($value)
    {
        $this->entry_contents = $value;
    }


     
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

    // ------------- implements --------------

    private $param_account_id;                  // ログインユーザーのアカウント
    private $param_company_name;                // 会社名

    // ログインユーザーのアカウント
    public function getParamAccountidAttribute()
    {
        return $this->param_account_id;
    }

    public function setParamAccountidAttribute($value)
    {
        $this->param_account_id = $value;
    }

    // 会社名
    public function getParamCompanynameAttribute()
    {
        return $this->param_company_name;
    }

    public function setParamCompanynameAttribute($value)
    {
        $this->param_company_name = $value;
    }

    // ------------- メソッド --------------

    /**
     * 会社情報登録
     *
     * @return void
     */
    public function insertCompany(){
        try {
            DB::table($this->table)->insert(
                [
                    'account_id' => $this->account_id,
                    'apply_term_from' => $this->apply_term_from,
                    'name' => $this->name,
                    'kana' => $this->kana,
                    'post_code' => $this->post_code,
                    'address1' => $this->address1,
                    'address2' => $this->address2,
                    'address_kana' => $this->address_kana,
                    'tel_no' => $this->tel_no,
                    'fax_no' => $this->fax_no,
                    'represent_name' => $this->represent_name,
                    'represent_kana' => $this->represent_kana,
                    'email' => $this->email,
                    'created_user' => $this->created_user,
                    'created_at' => $this->created_at
                ]
            );
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
     * 会社情報取得
     *
     * @return void
     */
    public function getCustomerInfoList(){
        try {
            $mainQuery = DB::table($this->table)
            ->selectRaw('company_name')
            ->selectRaw("max(account_id) as max_account_id");
            if (!empty($this->param_account_id)) {
                Log::debug('account_id = '.$this->param_account_id);
                $mainQuery->where($this->table.'.account_id',$this->param_account_id);
            }
            if (!empty($this->param_company_name)) {
                $mainQuery->where($this->table.'.company_name', 'like', '%'.$this->param_company_name.'%');
            }
            $result = $mainQuery->where($this->table.'.is_deleted',0)
                ->groupBy($this->table.'.company_name')
                ->orderBy('max_account_id', 'asc')
                ->get();
            return $result;
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

    }

    /**
     * 会社情報取得
     *
     * @return void
     */
    public function getCustomerInfo(){
        try {
            $mainQuery = DB::table($this->table)
            ->select(
                    'account_id',
                    'entry_type',
                    't2.code_name as entry_type_name',
                    'entry_date',
                    'entry_time',
                    'effective_from_date',
                    'effective_to_date',
                    'company_name',
                    'representative_name',
                    'phone_number',
                    'email_value',
                    'post_code',
                    'address_value',
                    'entry_contents'
            );
            $mainQuery 
                ->leftJoin($this->table_generalcodes.' as t2', function ($join) { 
                    $join->on('t2.code', '=', $this->table.'.entry_type')
                    ->where('t2.identification_id', '=', Config::get('const.C044.value'))
                    ->where($this->table.'.is_deleted', '=', 0)
                    ->where('t2.is_deleted', '=', 0);
                });
            if (!empty($this->param_account_id)) {
                Log::debug('account_id = '.$this->param_account_id);
                $mainQuery->where($this->table.'.account_id',$this->param_account_id);
            }
            if (!empty($this->param_company_name)) {
                $mainQuery->where($this->table.'.company_name', 'like', '%'.$this->param_company_name.'%');
            }
            $result = $mainQuery->where($this->table.'.is_deleted',0)
                ->orderBy($this->table.'.account_id', 'asc')
                ->orderBy($this->table.'.entry_date', 'desc')
                ->orderBy($this->table.'.entry_time', 'desc')
                ->get();
            return $result;
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

    }

    /**
     * 会社情報取得（適用期間）
     *
     * @return void
     */
    public function getCompanyInfoApply(){
        // 適用期間日付の取得
        try {
            $dt = null;
            if (isset($this->apply_term_from)) {
                $dt = new Carbon($this->apply_term_from);
            } else {
                $dt = new Carbon();
            }
            $target_date = $dt->format('Ymd');

            // companyの最大適用開始日付subquery
            $subquery = DB::table($this->table)
                ->selectRaw('MAX(apply_term_from) as max_apply_term_from')
                ->where('apply_term_from', '<=',$target_date)
                ->where('is_deleted', '=', 0);
            $mainquery = DB::table($this->table.' as t1')
                ->select('t1.name as name')
                ->JoinSub($subquery, 't2', function ($join) { 
                    $join->on('t1.apply_term_from', '=', 't2.max_apply_term_from');
                })
                ->where('t1.is_deleted', '=', 0)
                ->get();
            return $mainquery;
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 存在チェック
     *
     * @return boolean
     */
    public function isExistsInfo(){
        try {
            $is_exists = DB::table($this->table)
                ->where('is_deleted',0)
                ->exists();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_exists_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_exists_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

        return $is_exists;
    }

    /**
     * 削除
     *
     * @return void
     */
    public function delInfo(){
        try {
            DB::table($this->table)
                ->where('is_deleted',0)
                ->delete();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_delete_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_delete_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

}
