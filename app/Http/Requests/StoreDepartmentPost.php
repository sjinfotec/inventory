<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentPost extends FormRequest
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
            'name' => 'required|string|max:50|unique:departments,name'
        ];
    }

    /**
     * エラーメッセージ
     * override
     */
    public function messages(){
        return[
            'name.required'  => '部署名を入力してください',
            'name.max'  => '部署名の最大文字数は 50 です',
            'name.unique'  => '入力した部署名は既に使用済みです'
        ];
    }
}
