<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    private $no;                 // 申請番号

    // 申請番号
    public function getNoAttribute()
    {
        return $this->no;
    }

    public function setNoAttribute($value)
    {
        $this->no = $value;
    }

    private $doc_code;                 // 申請書類コード

    // 申請書類コード
    public function getDoccodeAttribute()
    {
        return $this->doc_code;
    }

    public function setDoccodeAttribute($value)
    {
        $this->doc_code = $value;
    }

    private $seq;                 // 承認順番

    // 承認順番
    public function getSeqAttribute()
    {
        return $this->seq;
    }

    public function setSeqAttribute($value)
    {
        $this->seq = $value;
    }

    private $log_no;                 // 履歴番号

    // 履歴番号
    public function getLognoAttribute()
    {
        return $this->log_no;
    }

    public function setLognoAttribute($value)
    {
        $this->log_no = $value;
    }

    private $status;                 // ステータス

    // ステータス
    public function getStatusAttribute()
    {
        return $this->status;
    }

    public function setStatusAttribute($value)
    {
        $this->status = $value;
    }

    private $department;                 // 承認者部署

    // 承認者部署
    public function getDepartmentAttribute()
    {
        return $this->department;
    }

    public function setDepartmentAttribute($value)
    {
        $this->department = $value;
    }

    private $user_code;                 // 承認者

    // 承認者
    public function getUsercodeAttribute()
    {
        return $this->user_code;
    }

    public function setUsercodeAttribute($value)
    {
        $this->user_code = $value;
    }

    private $approval_date;                 // 承認日または差し戻し日

    // 承認日または差し戻し日
    public function getApprovaldateAttribute()
    {
        return $this->approval_date;
    }

    public function setApprovaldateAttribute($value)
    {
        $this->approval_date = $value;
    }

    private $remand_reason;                 // 差し戻し理由

    // 差し戻し理由
    public function getRemandreasonAttribute()
    {
        return $this->remand_reason;
    }

    public function setRemandreasonAttribute($value)
    {
        $this->remand_reason = $value;
    }

    private $mail_result;                 // メール送信結果

    // メール送信結果
    public function getMailresultAttribute()
    {
        return $this->mail_result;
    }

    public function setMailresultAttribute($value)
    {
        $this->mail_result = $value;
    }

    private $mail_address;                 // メール宛先

    // メール宛先
    public function getMailaddressAttribute()
    {
        return $this->mail_address;
    }

    public function setMailaddressAttribute($value)
    {
        $this->mail_address = $value;
    }
}
