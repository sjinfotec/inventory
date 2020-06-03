<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('get_attendance_results', 'ApiGetAttendanceResultController');
Route::resource('card_register', 'ApiCardRegisterController');

// メール送信API
Route::post('/mail/inquiry', 'MailController@inquiry');

// 勤怠ログアップロード
Route::post('attendanceLogUpload', function () {
    $user_code = Auth::user()->code;
    $file_name = "winlog_".$user_code;
    try{
        request()->file->storeAs('private', $file_name);
    }catch(\Exception $e){
         Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.file_upload_error'));
         throw $e;
    }
});