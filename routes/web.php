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

Route::get('/', 'DailyWorkingInformationController@index')->middleware('auth');
Route::get('/home', 'HomeController@index')->name('home');
// 日次集計
Route::get('/daily', 'DailyWorkingInformationController@index')->middleware('auth');
Route::get('/daily/calc', 'DailyWorkingInformationController@show')->middleware('auth');
Route::get('/daily/show', 'DailyWorkingInformationController@show')->middleware('auth');
// 月次集計
Route::get('/monthly', 'MonthlyWorkingInformationController@index')->middleware('auth');
Route::get('/monthly/calc', 'MonthlyWorkingInformationController@calc')->middleware('auth');
Route::get('/monthly/show', 'MonthlyWorkingInformationController@show')->middleware('auth');
// 日次警告通知
Route::get('/daily_alert', 'DailyWorkingAlertController@index')->middleware('auth');
Route::get('/daily_alert/show', 'DailyWorkingAlertController@show')->middleware('auth');
// 月次警告通知
Route::get('/monthly_alert', 'MonthlyWorkingAlertController@index')->middleware('auth');
Route::get('/monthly_alert/show', 'MonthlyWorkingAlertController@show')->middleware('auth');
// 勤怠編集
Route::get('/edit_work_times', 'EditWorkTimesController@index')->middleware('auth');
Route::get('/edit_work_times/get', 'EditWorkTimesController@get')->middleware('auth');
Route::post('/edit_work_times/store', 'EditWorkTimesController@store')->middleware('auth');
Route::post('/edit_work_times/del', 'EditWorkTimesController@del')->middleware('auth');
Route::post('/edit_work_times/add', 'EditWorkTimesController@add')->middleware('auth');

// シフト
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
// ユーザー
Route::get('/user_add', 'UserAddController@index')->middleware('auth');
Route::post('/user_add/store', 'UserAddController@store')->middleware('auth');
Route::get('/user_add/get', 'UserAddController@getUserDetails')->middleware('auth');
Route::post('/user_add/del', 'UserAddController@del')->middleware('auth');
Route::post('/user_add/edit', 'UserAddController@edit')->middleware('auth');
Route::post('/user_add/fix', 'UserAddController@fixUser')->middleware('auth');
Route::post('/user_add/release_card_info', 'UserAddController@releaseCardInfo')->middleware('auth');
Route::get('/edit_user', 'UserAddController@index')->middleware('auth');
Route::post('/edit_user/store', 'UserAddController@store')->middleware('auth');
Route::get('/edit_user/get', 'UserAddController@getUserDetails')->middleware('auth');
Route::post('/edit_user/del', 'UserAddController@del')->middleware('auth');
Route::post('/edit_user/edit', 'UserAddController@edit')->middleware('auth');
Route::post('/edit_user/fix', 'UserAddController@fixUser')->middleware('auth');
Route::post('/edit_user/release_card_info', 'UserAddController@releaseCardInfo')->middleware('auth');
// ユーザー権限取得
Route::get('/get_login_user_role', 'ApiCommonController@getLoginUserRole')->middleware('auth');
// ユーザー部署権限取得
Route::get('/get_login_user_department', 'ApiCommonController@getLoginUserDepartment')->middleware('auth');
// ユーザーパスワード変更
Route::get('/user_pass', 'UserPassController@index')->middleware('auth');
Route::post('/user_pass/passchange', 'UserPassController@passChange')->middleware('auth');
// 部署
Route::get('/create_department', 'CreateDepartmentController@index')->middleware('auth');
Route::get('/create_department/get', 'CreateDepartmentController@getDetails')->middleware('auth');
Route::post('/create_department/store', 'CreateDepartmentController@store')->middleware('auth');
Route::post('/create_department/fix', 'CreateDepartmentController@fix')->middleware('auth');
Route::post('/create_department/del', 'CreateDepartmentController@del')->middleware('auth');
Route::post('/create_department/edit', 'CreateDepartmentController@edit')->middleware('auth');
Route::get('/create_department/get_apply', 'CreateDepartmentController@getDepartmentApplyTerm')->middleware('auth');
// カレンダー登録
Route::get('/create_calendar', 'CreateCalendarController@index')->middleware('auth');
Route::post('/create_calendar/store', 'CreateCalendarController@store')->middleware('auth');
Route::post('/create_calendar/init', 'CreateCalendarController@init')->middleware('auth');
// カレンダー編集
Route::get('/edit_calendar', 'EditCalendarController@index')->middleware('auth');
Route::get('/edit_calendar/get', 'EditCalendarController@getDetail')->middleware('auth');
Route::post('/edit_calendar/store', 'EditCalendarController@store')->middleware('auth');
// 会社情報
Route::get('/create_company_information', 'CreateCompanyInformationController@index')->middleware('auth');
Route::get('/create_company_information/get', 'CreateCompanyInformationController@getCompanyInfo')->middleware('auth');
Route::post('/create_company_information/store', 'CreateCompanyInformationController@store')->middleware('auth');
// 設定
Route::get('/setting_calc', 'SettingCalcController@index')->middleware('auth');
Route::get('/setting_calc/get', 'SettingCalcController@get')->middleware('auth');
Route::post('/setting_calc/store', 'SettingCalcController@store')->middleware('auth');
// タイムテーブル
Route::get('/create_time_table', 'CreateTimeTableController@index')->middleware('auth');
Route::get('/create_time_table/get', 'CreateTimeTableController@getDetail')->middleware('auth');
Route::post('/create_time_table/store', 'CreateTimeTableController@store')->middleware('auth');
Route::post('/create_time_table/del', 'CreateTimeTableController@del')->middleware('auth');
Route::post('/create_time_table/fix', 'CreateTimeTableController@fix')->middleware('auth');
Route::post('/create_time_table/add', 'CreateTimeTableController@add')->middleware('auth');
// リスト取得
Route::get('/get_departments_list', 'ApiCommonController@getDepartmentList')->middleware('auth');
Route::get('/get_employment_status_list', 'ApiCommonController@getEmploymentStatusList')->middleware('auth');
Route::get('/get_time_table_list', 'ApiCommonController@getTimeTableList')->middleware('auth');
Route::get('/get_business_day_list', 'ApiCommonController@getBusinessDayList')->middleware('auth');
Route::get('/get_holi_day_list', 'ApiCommonController@getHoliDayList')->middleware('auth');
Route::get('/get_time_unit_list', 'ApiCommonController@getTimeUnitList')->middleware('auth');
Route::get('/get_time_rounding_list', 'ApiCommonController@getTimeRoundingList')->middleware('auth');
Route::get('/get_user_leave_kbn', 'ApiCommonController@getUserLeaveKbnList')->middleware('auth');
Route::get('/get_mode_list', 'ApiCommonController@getModeList')->middleware('auth');
Route::get('/get_general_list', 'ApiCommonController@getRequestGeneralList')->middleware('auth');
Route::get('/get_demand_list', 'ApiCommonController@getDemandList')->middleware('auth');
Route::get('/get_confirm_list', 'ApiCommonController@getConfirmlList')->middleware('auth');
Route::get('/get_company_info_apply', 'ApiCommonController@getCompanyInfoApply')->middleware('auth');
// 締日取得
Route::get('/get_closing_day', 'ApiCommonController@getClosingDay')->middleware('auth');
// 申請
Route::get('/demand', 'DemandController@index')->middleware('auth');
Route::get('/demand/list_demand', 'DemandController@listDemand')->middleware('auth');
Route::post('/demand/make_demand', 'DemandController@makeDemand')->middleware('auth');
// 承認
Route::get('/approval', 'ApprovalController@index')->middleware('auth');
Route::get('/approval/list_approval', 'ApprovalController@listApproval')->middleware('auth');
Route::post('/approval/make_approval', 'ApprovalController@makeApproval')->middleware('auth');
// 承認設定
Route::get('/confirm', 'ConfirmController@index')->middleware('auth');
Route::get('/confirm/show', 'ConfirmController@show')->middleware('auth');
Route::post('/confirm/store', 'ConfirmController@store')->middleware('auth');

