<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('home');
// });

Auth::routes();
// Route::get('/{any}', function () {
//     return view('app');
// })->where('any', '.*');

Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/daily', 'DailyWorkingInformationController@index')->middleware('auth');
Route::get('/daily/calc', 'DailyWorkingInformationController@show')->middleware('auth');
Route::get('/daily/show', 'DailyWorkingInformationController@show')->middleware('auth');

Route::get('/monthly', 'MonthlyWorkingInformationController@index')->middleware('auth');
Route::get('/create_shift_time', 'CreateShiftTimeController@index')->middleware('auth');
Route::post('/create_shift_time/store', 'CreateShiftTimeController@store')->middleware('auth');
Route::get('/create_shift_time/get', 'CreateShiftTimeController@get')->middleware('auth');
Route::post('/create_shift_time/del', 'CreateShiftTimeController@del')->middleware('auth');
Route::get('/setting_shift_time', 'SttingShiftTimeController@index')->middleware('auth');
Route::get('/get_user_list', 'ApiCommonController@getUserList')->middleware('auth');
Route::post('/get_user_shift', 'ApiCommonController@getShiftInformation')->middleware('auth');
Route::post('/setting_shift_time/del', 'SttingShiftTimeController@del')->middleware('auth');
Route::post('/setting_shift_time/store', 'SttingShiftTimeController@store')->middleware('auth');
Route::post('/setting_shift_time/range_del', 'SttingShiftTimeController@rangeDel')->middleware('auth');
Route::get('/user_add', 'UserAddController@index')->middleware('auth');
Route::post('/user_add/store', 'UserAddController@store')->middleware('auth');
Route::get('/user_add/get', 'UserAddController@getUserDetails')->middleware('auth');
Route::post('/user_add/del', 'UserAddController@del')->middleware('auth');
Route::post('/user_add/edit', 'UserAddController@edit')->middleware('auth');
Route::get('/create_department', 'CreateDepartmentController@index')->middleware('auth');
Route::get('/create_calendar', 'CreateCalendarController@index')->middleware('auth');
Route::get('/create_employment_status', 'CreateEmploymentStatusController@index')->middleware('auth');

// リスト取得
Route::get('/get_departments_list', 'ApiCommonController@getDepartmentList')->middleware('auth');
Route::get('/get_employment_status_list', 'ApiCommonController@getEmploymentStatusList')->middleware('auth');
Route::get('/get_time_table_list', 'ApiCommonController@getTimeTableList')->middleware('auth');

