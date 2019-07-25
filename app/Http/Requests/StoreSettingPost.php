<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSettingPost extends FormRequest
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
            'year' => 'required',
            'biginningMonth' => 'required',
            'interval' => 'required|numeric|between:0.00,99.99',
        ];
    }

    /**
     * エラーメッセージ
     * override
     */
    public function messages(){
        return[
            'year.required'  => '年度を入力してください',
            'biginningMonth.required'  => '期首月を入力してください',
            'interval.required'  => '勤務間インターバルを入力してください',
            'interval.numeric'  => '数値を入力してください',
            'interval.between'  => '0 ～　99.99 までの数値を入力してください'
        ];
    }
}
