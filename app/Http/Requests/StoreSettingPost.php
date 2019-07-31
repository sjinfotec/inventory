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
            'threeMonthTotal' => 'nullable|numeric|between:0.00,99999.99',
            'sixMonthTotal' => 'nullable|numeric|between:0.00,99999.99',
            'yaerTotal' => 'nullable|numeric|between:0.00,99999.99',
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
            'threeMonthTotal.numeric'  => '数値を入力してください',
            'threeMonthTotal.between'  => '0 ～　99999.99 までの数値を入力してください',
            'sixMonthTotal.numeric'  => '数値を入力してください',
            'sixMonthTotal.between'  => '0 ～　99999.99 までの数値を入力してください',
            'yaerTotal.numeric'  => '数値を入力してください',
            'yaerTotal.between'  => '0 ～　99999.99 までの数値を入力してください',
            'interval.required'  => '勤務間インターバルを入力してください',
            'interval.numeric'  => '数値を入力してください',
            'interval.between'  => '0 ～　99.99 までの数値を入力してください'
        ];
    }
}
