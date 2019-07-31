<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Company extends Model
{
    protected $table = 'companies';
    
    private $name;                  
     
    public function getNameAttribute()
    {
        return $this->name;
    }

    public function setNameAttribute($value)
    {
        $this->name = $value;
    }

    private $kana;                  

     
    public function getKanaAttribute()
    {
        return $this->kana;
    }

    public function setKanaAttribute($value)
    {
        $this->kana = $value;
    }

    private $post_code;                  

     
    public function getPostcodeAttribute()
    {
        return $this->post_code;
    }

    public function setPostcodeAttribute($value)
    {
        $this->post_code = $value;
    }

    private $address1;                  

     
    public function getAddress1Attribute()
    {
        return $this->address1;
    }

    public function setAddress1Attribute($value)
    {
        $this->address1 = $value;
    }

    private $address2;                  

     
    public function getAddress2Attribute()
    {
        return $this->address2;
    }

    public function setAddress2Attribute($value)
    {
        $this->address2 = $value;
    }

    private $address_kana;                  

     
    public function getAddresskanaAttribute()
    {
        return $this->address_kana;
    }

    public function setAddresskanaAttribute($value)
    {
        $this->address_kana = $value;
    }

    private $tel_no;                  

     
    public function getTelnoAttribute()
    {
        return $this->tel_no;
    }

    public function setTelnoAttribute($value)
    {
        $this->tel_no = $value;
    }

    private $fax_no;                  

     
    public function getFaxnoAttribute()
    {
        return $this->fax_no;
    }

    public function setFaxnoAttribute($value)
    {
        $this->fax_no = $value;
    }

    private $represent_name;                  

     
    public function getRepresentnameAttribute()
    {
        return $this->represent_name;
    }

    public function setRepresentnameAttribute($value)
    {
        $this->represent_name = $value;
    }

    private $represent_kana;                  

     
    public function getRepresentkanaAttribute()
    {
        return $this->represent_kana;
    }

    public function setRepresentkanaAttribute($value)
    {
        $this->represent_kana = $value;
    }

    private $email;                  

     
    public function getEmailAttribute()
    {
        return $this->email;
    }

    public function setEmailAttribute($value)
    {
        $this->email = $value;
    }

    private $month_sm;                  

     
    public function getMonthsmAttribute()
    {
        return $this->month_sm;
    }

    public function setMonthsmAttribute($value)
    {
        $this->month_sm = $value;
    }

    private $created_user;                  

     
    public function getCreateduserAttribute()
    {
        return $this->created_user;
    }

    public function setCreateduserAttribute($value)
    {
        $this->created_user = $value;
    }

    private $updated_user;                  

     
    public function getUpdateduserAttribute()
    {
        return $this->updated_user;
    }

    public function setUpdateduserAttribute($value)
    {
        $this->updated_user = $value;
    }

    private $created_at;                  

     
    public function getCreatedatAttribute()
    {
        return $this->created_at;
    }

    public function setCreatedatAttribute($value)
    {
        $this->created_at = $value;
    }

    private $updated_at;                  

     
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
