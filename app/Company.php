<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Company extends Model
{
    protected $table = 'companies';

    private $apply_term_from;                   // 適用期間開始
    private $apply_term_to;                     // 適用期間終了
    private $name;                              // 会社名
    private $kana;                              // 会社カナ
    private $post_code;                         // 郵便番号
    private $address1;                          // 住所１
    private $address2;                          // 住所２
    private $address_kana;                      // 住所カナ
    private $tel_no;                            // 電話番号
    private $fax_no;                            // FAX番号
    private $represent_name;                    // 代表者氏名
    private $represent_kana;                    // 代表者カナ
    private $email;                             // e-mail
    private $month_sm;                          // 月締め
    private $created_user;                      // 作成ユーザー
    private $updated_user;                      // 修正ユーザー
    private $created_at;                        // 作成日時
    private $updated_at;                        // 修正日時

    // 適用期間開始
    public function getApplytermfromAttribute()
    {
        return $this->apply_term_from;
    }

    public function setApplytermfromAttribute($value)
    {
        $this->apply_term_from = $value;
    }


    // 適用期間終了
    public function getApplytermtoAttribute()
    {
        return $this->apply_term_to;
    }

    public function setApplytermtoAttribute($value)
    {
        $this->apply_term_to = $value;
    }
    
     
    // 会社名
    public function getNameAttribute()
    {
        return $this->name;
    }

    public function setNameAttribute($value)
    {
        $this->name = $value;
    }


    // 会社カナ 
    public function getKanaAttribute()
    {
        return $this->kana;
    }

    public function setKanaAttribute($value)
    {
        $this->kana = $value;
    }


    // 郵便番号 
    public function getPostcodeAttribute()
    {
        return $this->post_code;
    }

    public function setPostcodeAttribute($value)
    {
        $this->post_code = $value;
    }


    // 住所１ 
    public function getAddress1Attribute()
    {
        return $this->address1;
    }

    public function setAddress1Attribute($value)
    {
        $this->address1 = $value;
    }


    // 住所２ 
    public function getAddress2Attribute()
    {
        return $this->address2;
    }

    public function setAddress2Attribute($value)
    {
        $this->address2 = $value;
    }


    // 住所カナ 
    public function getAddresskanaAttribute()
    {
        return $this->address_kana;
    }

    public function setAddresskanaAttribute($value)
    {
        $this->address_kana = $value;
    }


    // 電話番号 
    public function getTelnoAttribute()
    {
        return $this->tel_no;
    }

    public function setTelnoAttribute($value)
    {
        $this->tel_no = $value;
    }


    // FAX番号 
    public function getFaxnoAttribute()
    {
        return $this->fax_no;
    }

    public function setFaxnoAttribute($value)
    {
        $this->fax_no = $value;
    }


    // 代表者氏名 
    public function getRepresentnameAttribute()
    {
        return $this->represent_name;
    }

    public function setRepresentnameAttribute($value)
    {
        $this->represent_name = $value;
    }


    // 代表者カナ 
    public function getRepresentkanaAttribute()
    {
        return $this->represent_kana;
    }

    public function setRepresentkanaAttribute($value)
    {
        $this->represent_kana = $value;
    }


    // e-mail 
    public function getEmailAttribute()
    {
        return $this->email;
    }

    public function setEmailAttribute($value)
    {
        $this->email = $value;
    }


    // 月締め 
    public function getMonthsmAttribute()
    {
        return $this->month_sm;
    }

    public function setMonthsmAttribute($value)
    {
        $this->month_sm = $value;
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

    /**
     * 会社情報登録
     *
     * @return void
     */
    public function insertCompany(){
        DB::table($this->table)->insert(
            [
                'apply_term_from' => $this->apply_term_from,
                'apply_term_to' => $this->apply_term_to,
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
    }

    /**
     * 会社情報取得
     *
     * @return void
     */
    public function getCompanyInfo(){
        $data = DB::table($this->table)
        ->select(
                'apply_term_from',
                'apply_term_to',
                'name',
                'kana',
                'post_code',
                'address1',
                'address2',
                'address_kana',
                'tel_no',
                'fax_no',
                'represent_name',
                'represent_kana',
                'email'
        )
        ->where('is_deleted',0)
        ->get();
    
        return $data;
    }

    /**
     * 存在チェック
     *
     * @return boolean
     */
    public function isExistsInfo(){
        $is_exists = DB::table($this->table)
            ->where('is_deleted',0)
            ->exists();

        return $is_exists;
    }

    /**
     * 削除
     *
     * @return void
     */
    public function delInfo(){
        DB::table($this->table)
            ->where('is_deleted',0)
            ->delete();
    }

}
