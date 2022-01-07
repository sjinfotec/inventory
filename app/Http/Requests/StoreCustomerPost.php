<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class StoreCustomerPost extends FormRequest
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
            'name' => 'required|string|max:30',
            'code' => ['required',Rule::unique('users')->ignore($this->id),'max:10'],
        ];
    }

    /**
     * エラーメッセージ
     * override
     */
    public function messages(){
        return[
            'name.required'  => '客先名を入力してください',
            'name.max'  => '客先名の最大文字数は 30 です',
            'code.required'  => 'ログインIDを入力してください',
            'code.unique'  => 'ログインIDは既に使用済です',
            'code.max'  => 'ログインIDの最大文字数は 10 です',
        ];
    }
}
