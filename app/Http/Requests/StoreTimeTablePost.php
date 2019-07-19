<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTimeTablePost extends FormRequest
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
            'no' => ['required',Rule::unique('working_timetables')->ignore($this),'max:10'],
            'name' => 'required|string|max:191'
        ];
    }

    /**
     * エラーメッセージ
     * override
     */
    public function messages(){
        return[
            'no.required'  => 'No を入力してください',
            'no.unique'  => 'No は既に使用済です',
            'no.max'  => 'No の最大文字数は 10 です',
            'name.required'  => 'タイムテーブル名称を入力してください',
            'name.max'  => 'タイムテーブル名称の最大文字数は 191 です',
        ];
    }
}
