<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\Inquiry;
use Validator;
use Illuminate\Mail\Mailable;


class MailController extends Controller
{
    //バリデーション定義  
    const INQUIRY_VALIDATIONS = [        
       'email' => 'required|email|max:255'
    ];


    public function inquiry(Request $request)
    {
        // バリデーション実行
        $validator = Validator::make($request->only('login_id','email'), self::INQUIRY_VALIDATIONS);

        // 失敗時
        if($validator->fails()){
            return response()->json([
                'result' => false,
                'errors' => self::formatErrors($validator->errors())
            ]);
        }
        // 成功時
        Mail::to($request->input('email'))->send(new Inquiry($request));
        return response()->json([
            'result' => true,
            'errors' => []
        ]);
    }
}
