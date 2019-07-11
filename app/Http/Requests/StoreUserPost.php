<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class StoreUserPost extends FormRequest
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
            'kana' => 'required',
            'email' => 'required|email',
            'status' => 'required',
            // 'loginid' => 'required|unique:users,code|max:10',
            'code' => ['required',Rule::unique('users')->ignore($this->id),'max:10'],
            'password' => 'required|max:30',
        ];
    }

    /**
     * エラーメッセージ
     * override
     */
    public function messages(){
        return[
            'name.required'  => '社員名を入力してください',
            'name.max'  => '社員名の最大文字数は 30 です',
            'kana.required'  => 'ふりがなを入力してください',
            'email.required'  => 'メールアドレスを入力してください',
            'email.email'  => 'メールアドレスの入力形式で入力してください (例: sanjyo-tarou@ssjjoo.com)',
            'status.required' => '雇用形態を選択してください',
            'code.required'  => 'ログインIDを入力してください',
            'code.unique'  => 'ログインIDは既に使用済です',
            'code.max'  => 'ログインIDの最大文字数は 10 です',
            'password.required'  => 'パスワードを入力してください',
            'password.max'  => 'パスワードの最大文字数は 30 です',
        ];
    }
}
