<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\ApiCommonController;

class ApprovalAuthorizer extends Model
{
    protected $table = 'approval_authorizers';

    private $id;                                // id
    private $account_id;                        // ログインユーザーのアカウント
    private $approval_route_no;                 // 承認ルート番号
    private $seq;                               // 承認順番
    private $main_sub;                          // 正副
    private $approval_department_code;          // 承認部署
    private $approval_user_code;                // 承認者
    private $created_user;    
    private $updated_user;                  
    private $created_at;                  
    private $updated_at;                  
    private $is_deleted;                  

    // id
    public function getIdAttribute()
    {
        return $this->id;
    }

    public function setIdAttribute($value)
    {
        $this->id = $value;
    }

    // ログインユーザーのアカウント
    public function getAccountidAttribute()
    {
        return $this->account_id;
    }

    public function setAccountidAttribute($value)
    {
        $this->account_id = $value;
    }

    // 承認ルート番号
    public function getApprovalroutenoAttribute()
    {
        return $this->approval_route_no;
    }

    public function setApprovalroutenoAttribute($value)
    {
        $this->approval_route_no = $value;
    }

    // 承認順番
    public function getSeqAttribute()
    {
        return $this->seq;
    }

    public function setSeqAttribute($value)
    {
        $this->seq = $value;
    }

    // 正副
    public function getMainsubAttribute()
    {
        return $this->main_sub;
    }

    public function setMainsubAttribute($value)
    {
        $this->main_sub = $value;
    }

    // 承認部署
    public function getApprovaldepartmentcodeAttribute()
    {
        return $this->approval_department_code;
    }

    public function setApprovaldepartmentcodeAttribute($value)
    {
        $this->approval_department_code = $value;
    }

    // 承認者
    public function getApprovalusercodeAttribute()
    {
        return $this->approval_user_code;
    }

    public function setApprovalusercodeAttribute($value)
    {
        $this->approval_user_code = $value;
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
     
    // 作成時間
    public function getCreatedatAttribute()
    {
        return $this->created_at;
    }
    public function setCreatedatAttribute($value)
    {
        $this->created_at = $value;
    }
     
    // 修正時間
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

    // ------------- implements --------------

    private $param_id;                          // id
    private $param_account_id;                  // ログインユーザーのアカウント
    private $param_approval_route_no;           // 承認ルート番号
    private $param_seq;                         // 承認順番
    private $param_main_sub;                    // 正副
    private $param_approval_department_code;    // 承認部署
    private $param_approval_user_code;          // 承認者

    // id
    public function getParamidAttribute()
    {
        return $this->param_id;
    }

    public function setParamidAttribute($value)
    {
        $this->param_id = $value;
    }

    // ログインユーザーのアカウント
    public function getParamAccountidAttribute()
    {
        return $this->param_account_id;
    }

    public function setParamAccountidAttribute($value)
    {
        $this->param_account_id = $value;
    }

    // 承認ルート番号
    public function getParamapprovalroutenoAttribute()
    {
        return $this->param_approval_route_no;
    }

    public function setParamapprovalroutenoAttribute($value)
    {
        $this->param_approval_route_no = $value;
    }

    // 承認順番
    public function getParamseqAttribute()
    {
        return $this->param_seq;
    }

    public function setParamseqAttribute($value)
    {
        $this->param_seq = $value;
    }

    // 正副
    public function getParammainsubAttribute()
    {
        return $this->param_seq;
    }

    public function setParammainsubAttribute($value)
    {
        $this->param_seq = $value;
    }


    // 承認部署
    public function getParamapprovaldepartmentcodeAttribute()
    {
        return $this->param_approval_department_code;
    }

    public function setParamapprovaldepartmentcodeAttribute($value)
    {
        $this->param_approval_department_code = $value;
    }


    // 承認者
    public function getParamapprovalusercodeAttribute()
    {
        return $this->param_approval_user_code;
    }

    public function setParamapprovalusercodeAttribute($value)
    {
        $this->param_approval_user_code = $value;
    }

    // ------------- method --------------

}
