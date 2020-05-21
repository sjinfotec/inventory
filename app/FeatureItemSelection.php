<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class FeatureItemSelection extends Model
{
    protected $table = 'feature_item_selections';
    protected $table_users = 'users';
    protected $guarded = array('id');

    private $id;                        // ID
    private $account_id;                // アカウントID
    private $selection_code;            // 選択種類
    private $item_code;                 // 項目コード
    private $item_seq;                  // 出力順
    private $item_name;                 // 項目名
    private $item_kanji_name;           // 項目漢字名
    private $item_out_name;             // 出力項目名
    private $value_select;              // 項目選択値
    private $created_user;              // 作成ユーザー
    private $updated_user;              // 修正ユーザー
    private $created_at;                // 作成日時
    private $updated_at;                // 修正日時
    private $is_deleted;                // 削除フラグ

    // ID
    public function getIdAttribute()
    {
        return $this->id;
    }

    public function setIdAttribute($value)
    {
        $this->id = $value;
    }


    // アカウントID
    public function getAccountidAttribute()
    {
        return $this->account_id;
    }

    public function setAccountidAttribute($value)
    {
        $this->account_id = $value;
    }


    // 選択種類
    public function getSelectioncodeAttribute()
    {
        return $this->selection_code;
    }

    public function setSelectioncodeAttribute($value)
    {
        $this->selection_code = $value;
    }


    // 項目コード
    public function getItemcodeAttribute()
    {
        return $this->item_code;
    }

    public function setItemcodeAttribute($value)
    {
        $this->item_code = $value;
    }


    // 出力順
    public function getItemseqAttribute()
    {
        return $this->item_seq;
    }

    public function setItemseqAttribute($value)
    {
        $this->item_seq = $value;
    }


    // 項目名
    public function getItemnameAttribute()
    {
        return $this->item_name;
    }

    public function setItemnameAttribute($value)
    {
        $this->item_name = $value;
    }


    // 項目漢字名
    public function getItemkanjinameAttribute()
    {
        return $this->item_kanji_name;
    }

    public function setItemkanjinameAttribute($value)
    {
        $this->item_kanji_name = $value;
    }


    // 出力項目名
    public function getItemoutnameAttribute()
    {
        return $this->item_out_name;
    }

    public function setItemoutnameAttribute($value)
    {
        $this->item_out_name = $value;
    }


    // 項目選択値
    public function getValueselectAttribute()
    {
        return $this->value_select;
    }

    public function setValueselectAttribute($value)
    {
        $this->value_select = $value;
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


    // 削除フラグ
    public function getIsdeletedAttribute()
    {
        return $this->is_deleted;
    }

    public function setIsdeletedAttribute($value)
    {
        $this->is_deleted = $value;
    }

    private $param_account_id;          // アカウントID
    private $param_selection_code;      // 選択種類


    // アカウントID
    public function getParamaccountidAttribute()
    {
        return $this->param_account_id;
    }

    public function setParamaccountidAttribute($value)
    {
        $this->param_account_id = $value;
    }

    // 選択種類
    public function getParamselectioncodeAttribute()
    {
        return $this->param_selection_code;
    }

    public function setParamselectioncodeAttribute($value)
    {
        $this->param_selection_code = $value;
    }
    
    /**
     * 情報取得
     *
     * @return void
     */
    public function getItem(){
        try {
            $query = DB::table($this->table)
            ->select(
                    'account_id',
                    'selection_code',
                    'item_code',
                    'item_seq',
                    'item_name',
                    'item_kanji_name',
                    'item_out_name',
                    'value_select'
            );
            if ($this->param_account_id != null && $this->param_account_id != "") {
                $query->where('account_id', $this->param_account_id);
            }
            if ($this->param_selection_code != null && $this->param_selection_code != "") {
                $query->where('selection_code', $this->param_selection_code);
            }
            $data  = $query->where('is_deleted',0)
                    ->orderBy('account_id', 'asc')
                    ->orderBy('selection_code', 'asc')
                    ->orderBy('item_code', 'asc')
                    ->orderBy('item_seq', 'asc')
                    ->get();
            return $data;
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

}
