<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Http\Requests\StoreCompanyInfoPost;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Company;

class CreateCompanyInformationController extends Controller
{
    const SUCCESS = 0;
    const FAILED = 1;

    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        return view('create_company_information');
    }

    /**
     * 会社情報登録
     *
     * @param StoreCompanyInfoPost $request
     * @return void
     */
    public function store(StoreCompanyInfoPost $request){
        $inputs = collect();
        $inputs->put('company_name',$request->companyName);
        $inputs->put('company_kana',$request->companyKana);
        $inputs->put('post_code',$request->postCode);
        $inputs->put('address1', $request->address1);
        $inputs->put('address2',$request->address2);
        $inputs->put('address_kana',$request->addressKana);
        $inputs->put('tell',$request->tell);
        $inputs->put('fax',$request->fax);
        $inputs->put('representative_name',$request->representativeName);
        $inputs->put('representative_kana',$request->representativeKana);
        $inputs->put('email',$request->email);
    
        $result = $this->insert($inputs);
        
        if($result){
        }else{
            return false;
        }
    }

    /**
     * INSERT
     *
     * @param [type] $inputs
     * @return void
     */
    private function insert($inputs){
        $company = new Company();
        $systemdate = Carbon::now();
        $user = Auth::user();
        $user_code = $user->code;

        DB::beginTransaction();
        try{
            $company->setNameAttribute($inputs->get('company_name'));
            $company->setKanaAttribute($inputs->get('company_kana'));
            $company->setPostcodeAttribute($inputs->get('post_code'));
            $company->setAddress1Attribute($inputs->get('address1'));
            $company->setAddress2Attribute($inputs->get('address2'));
            $company->setAddresskanaAttribute($inputs->get('address_kana'));
            $company->setTelnoAttribute($inputs->get('tell'));
            $company->setFaxnoAttribute($inputs->get('fax'));
            $company->setRepresentnameAttribute($inputs->get('representative_name'));
            $company->setRepresentkanaAttribute($inputs->get('representative_kana'));
            $company->setEmailAttribute($inputs->get('email'));
            $company->setCreateduserAttribute($user_code);
            $company->setCreatedatAttribute($systemdate);
            $is_exists = $company->isExistsInfo();

            if($is_exists){
                $company->delInfo();
            }
            $company->insertCompany();
            DB::commit();
            return true;

        }catch(\PDOException $e){
            DB::rollBack();
            return false;
        }

    }

    /**
     * 会社情報取得
     *
     * @return void
     */
    public function getCompanyInfo(){
        $company = new Company();
        $result =  $company->getCompanyInfo();
        
        return $result;
    }
}
