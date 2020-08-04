<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyInfoPost extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'companyName' => 'required|max:191',
            'companyKana' => 'max:191',
            'postCode' => 'max:191',
            'address1' => 'max:191',
            'address2' => 'max:191',
            'addressKana' => 'max:191',
            'tell' => 'max:191',
            'fax' => 'max:191',
            'representativeName' => 'max:191',
            'representativeKana' => 'max:191',
            'email' => 'nullable|email|max:191',
        ];
    }

    /**
     * エラーメッセージ
     * override
     */
    public function messages(){
        return[
            'companyName.required'  => '会社名を入力してください',
            'companyName.max'  => '会社名の最大文字数は 191 です',
            'companyKana.max'  => '会社カナの最大文字数は 191 です',
            'postCode.max'  => '郵便番号の最大文字数は 191 です',
            'address1.max'  => '住所１の最大文字数は 191 です',
            'address2.max'  => '住所２の最大文字数は 191 です',
            'addressKana.max'  => '住所カナの最大文字数は 191 です',
            'tell.max'  => '電話番号の最大文字数は 191 です',
            'fax.max'  => 'FAX番号の最大文字数は 191 です',
            'representativeName.max'  => '代表者氏名の最大文字数は 191 です',
            'representativeKana.max'  => '代表者カナの最大文字数は 191 です',
            'email.email'  => 'メールアドレスの入力形式で入力してください (例: sanjyo-tarou@ssjjoo.com)',
            'email.max'  => 'メールアドレスの最大文字数は 191 です'
        ];
    }
}
