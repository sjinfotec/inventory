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

// ------------------ ホーム --------------------------------
Route::get('/', 'HomeController@index')->middleware('auth');
Route::get('/home', 'HomeController@index')->name('home');
// ------------------ 集計 --------------------------------
// 日次集計
Route::get('/daily', 'DailyWorkingInformationController@index')->middleware('auth');
Route::post('/daily/calc', 'DailyWorkingInformationController@show')->middleware('auth');
Route::get('/daily/show', 'DailyWorkingInformationController@show')->middleware('auth');
// 月次集計
Route::get('/monthly', 'MonthlyWorkingInformationController@index')->middleware('auth');
Route::post('/monthly/calc', 'MonthlyWorkingInformationController@calc')->middleware('auth');
Route::post('/monthly/show', 'MonthlyWorkingInformationController@show')->middleware('auth');
// 勤怠ログ集計
Route::get('/attendancelog', 'AttendanceLogController@index')->middleware('auth');
Route::post('/attendancelog/show', 'AttendanceLogController@show')->middleware('auth');
// ------------------ 警告 --------------------------------
// 日次警告通知
Route::get('/daily_alert', 'DailyWorkingAlertController@index')->middleware('auth');
Route::get('/daily_alert/home', 'DailyWorkingAlertController@homeindex')->middleware('auth');
Route::get('/daily_alert/alerthome', 'DailyWorkingAlertController@alerthome')->middleware('auth')->name('daily_alert.alerthome');
Route::post('/daily_alert/show', 'DailyWorkingAlertController@show')->middleware('auth');
// 月次警告通知
Route::get('/monthly_alert', 'MonthlyWorkingAlertController@index')->middleware('auth');
Route::get('/monthly_alert/show', 'MonthlyWorkingAlertController@show')->middleware('auth');
// ------------------ 申請 --------------------------------
// 申請
Route::get('/demand', 'DemandController@index')->middleware('auth');
Route::get('/demand/list_demand', 'DemandController@listDemand')->middleware('auth');
Route::post('/demand/make_demand', 'DemandController@makeDemand')->middleware('auth');
// 承認
Route::get('/approval', 'ApprovalController@index')->middleware('auth');
Route::get('/approval/list_approval', 'ApprovalController@listApproval')->middleware('auth');
Route::post('/approval/make_approval', 'ApprovalController@makeApproval')->middleware('auth');
// 承認設定 いずれ削除　ConfirmControllerも
Route::get('/confirm', 'ConfirmController@index')->middleware('auth');
Route::post('/confirm/gettable', 'ConfirmController@gettable')->middleware('auth');
Route::post('/confirm/show', 'ConfirmController@show')->middleware('auth');
Route::post('/confirm/store', 'ConfirmController@store')->middleware('auth');
Route::post('/confirm/del', 'ConfirmController@del')->middleware('auth');
// 承認設定
Route::get('/approvalroot', 'CreateApprovalRouteNoController@index')->middleware('auth');

// カレンダー登録
// Route::get('/setting-calendar', 'CreateCalendarController@index')->middleware('auth');
// Route::post('/setting-calendar/store', 'CreateCalendarController@store')->middleware('auth');
// Route::post('/setting-calendar/init', 'CreateCalendarController@init')->middleware('auth');
// カレンダー編集
// Route::get('/edit_calendar', 'EditCalendarController@index')->middleware('auth');
// Route::post('/edit_calendar/get', 'EditCalendarController@getDetail')->middleware('auth');
// Route::post('/edit_calendar/store', 'EditCalendarController@store')->middleware('auth');
// Route::post('/edit_calendar/fix', 'EditCalendarController@fix')->middleware('auth');
// Route::post('/edit_calendar/init', 'EditCalendarController@init')->middleware('auth');
// ------------------ 編集 --------------------------------
// 勤怠編集
Route::get('/edit_work_times', 'EditWorkTimesController@index')->middleware('auth');
Route::post('/edit_work_times/get', 'EditWorkTimesController@get')->middleware('auth');
Route::post('/edit_work_times/store', 'EditWorkTimesController@store')->middleware('auth');
Route::post('/edit_work_times/fix', 'EditWorkTimesController@fix')->middleware('auth');
Route::post('/edit_work_times/fixtime', 'EditWorkTimesController@fixtime')->middleware('auth');
Route::post('/edit_work_times/del', 'EditWorkTimesController@del')->middleware('auth');
Route::post('/edit_work_times/add', 'EditWorkTimesController@add')->middleware('auth');
// シフト
// Route::get('/create_shift_time', 'CreateShiftTimeController@index')->middleware('auth');        // 未使用
// Route::post('/create_shift_time/store', 'CreateShiftTimeController@store')->middleware('auth'); // 未使用
// Route::post('/create_shift_time/get', 'CreateShiftTimeController@get')->middleware('auth');     // 未使用
// Route::post('/create_shift_time/del', 'CreateShiftTimeController@del')->middleware('auth');     // 未使用
Route::get('/setting_shift_time', 'SttingShiftTimeController@index')->middleware('auth');
Route::post('/setting_shift_time/del', 'SttingShiftTimeController@del')->middleware('auth');
Route::post('/setting_shift_time/store', 'SttingShiftTimeController@store')->middleware('auth');
Route::post('/setting_shift_time/range_del', 'SttingShiftTimeController@rangeDel')->middleware('auth');
// 勤怠ログ登録
Route::get('/store_attendancelog', 'StoreAttendanceLogController@index')->middleware('auth');
Route::post('/store_attendancelog/store', 'StoreAttendanceLogController@store')->middleware('auth');
// 勤怠ログ編集
Route::get('/edit_attendancelog', 'EditAttendanceLogController@index')->middleware('auth');
Route::post('/edit_attendancelog/get', 'EditAttendanceLogController@get')->middleware('auth');
Route::post('/edit_attendancelog/store', 'EditAttendanceLogController@store')->middleware('auth');
Route::post('/edit_attendancelog/fix', 'EditAttendanceLogController@fix')->middleware('auth');
// ------------------ 設定 --------------------------------
// 会社情報
Route::get('/create_company_information', 'CreateCompanyInformationController@index')->middleware('auth');
Route::post('/create_company_information/get', 'CreateCompanyInformationController@getCompanyInfo')->middleware('auth');
Route::post('/create_company_information/store', 'CreateCompanyInformationController@store')->middleware('auth');
// 部署
Route::get('/create_department', 'CreateDepartmentController@index')->middleware('auth');
Route::post('/create_department/get', 'CreateDepartmentController@getDetails')->middleware('auth');
Route::post('/create_department/store', 'CreateDepartmentController@store')->middleware('auth');
Route::post('/create_department/fix', 'CreateDepartmentController@fix')->middleware('auth');
Route::post('/create_department/del', 'CreateDepartmentController@del')->middleware('auth');
Route::post('/create_department/edit', 'CreateDepartmentController@edit')->middleware('auth');
// 労働時間基本設定
Route::get('/setting_calc', 'SettingCalcController@index')->middleware('auth');
Route::post('/setting_calc/get', 'SettingCalcController@get')->middleware('auth');
Route::post('/setting_calc/store', 'SettingCalcController@store')->middleware('auth');
// 勤務帯時間設定
Route::get('/create_time_table', 'CreateTimeTableController@index')->middleware('auth');
Route::post('/create_time_table/get', 'CreateTimeTableController@getDetail')->middleware('auth');
Route::post('/create_time_table/store', 'CreateTimeTableController@store')->middleware('auth');
Route::post('/create_time_table/del', 'CreateTimeTableController@del')->middleware('auth');
Route::post('/create_time_table/fix', 'CreateTimeTableController@fix')->middleware('auth');
Route::post('/create_time_table/add', 'CreateTimeTableController@add')->middleware('auth');
// カレンダー設定
Route::get('/setting_calendar', 'EditCalendarController@index')->middleware('auth');
Route::post('/setting_calendar/get', 'EditCalendarController@getDetail')->middleware('auth');
Route::post('/setting_calendar/fix', 'EditCalendarController@fix')->middleware('auth');
Route::post('/setting_calendar/fixbatch', 'EditCalendarController@fixbatch')->middleware('auth');
Route::post('/setting_calendar/init', 'EditCalendarController@init')->middleware('auth');
Route::post('/setting_calendar/copyinit', 'EditCalendarController@copyinit')->middleware('auth');
// ユーザー情報設定
// Route::get('/user_add', 'UserAddController@index')->middleware('auth');
// Route::post('/user_add/store', 'UserAddController@store')->middleware('auth');
// Route::post('/user_add/get', 'UserAddController@getUserDetails')->middleware('auth');
// Route::post('/user_add/del', 'UserAddController@del')->middleware('auth');
// Route::post('/user_add/edit', 'UserAddController@edit')->middleware('auth');
// Route::post('/user_add/fix', 'UserAddController@fixUser')->middleware('auth');
// Route::post('/user_add/release_card_info', 'UserAddController@releaseCardInfo')->middleware('auth');
Route::get('/edit_user', 'UserAddController@index')->middleware('auth');
Route::post('/edit_user/store', 'UserAddController@store')->middleware('auth');
Route::post('/edit_user/get', 'UserAddController@getUserDetails')->middleware('auth');
Route::post('/edit_user/del', 'UserAddController@del')->middleware('auth');
Route::post('/edit_user/edit', 'UserAddController@edit')->middleware('auth');
Route::post('/edit_user/fix', 'UserAddController@fixUser')->middleware('auth');
Route::post('/edit_user/release_card_info', 'UserAddController@releaseCardInfo')->middleware('auth');
Route::post('/edit_user/up', 'UserAddController@up')->middleware('auth');
// ------------------ 操作 --------------------------------
// ユーザーパスワード変更
Route::get('/user_pass', 'UserPassController@index')->middleware('auth');
Route::post('/user_pass/passchange', 'UserPassController@passChange')->middleware('auth');
// ファイルダウンロード
Route::get('/file_download', 'FileDownloadController@index')->middleware('auth');
Route::get('/file_download/getdownload', 'FileDownloadController@getfileDownload')->middleware('auth');
// ------------------ 共通 --------------------------------
// リスト取得
Route::post('/get_departments_list', 'ApiCommonController@getDepartmentList')->middleware('auth');
Route::post('/get_employment_status_list', 'ApiCommonController@getEmploymentStatusList')->middleware('auth');
Route::post('/get_time_table_list', 'ApiCommonController@getTimeTableList')->middleware('auth');
Route::post('/get_business_day_list', 'ApiCommonController@getBusinessDayList')->middleware('auth');
Route::post('/get_holi_day_list', 'ApiCommonController@getHoliDayList')->middleware('auth');
Route::post('/get_user_leave_kbn', 'ApiCommonController@getUserLeaveKbnList')->middleware('auth');
Route::post('/get_mode_list', 'ApiCommonController@getModeList')->middleware('auth');
Route::post('/get_general_list', 'ApiCommonController@getRequestGeneralList')->middleware('auth');
Route::post('/get_demand_list', 'ApiCommonController@getDemandList')->middleware('auth');
Route::post('/get_confirm_list', 'ApiCommonController@getConfirmlList')->middleware('auth');
Route::post('/get_company_info_apply', 'ApiCommonController@getCompanyInfoApply')->middleware('auth');
Route::post('/approval_root_list', 'ApiCommonController@getApprovalroutenoList')->middleware('auth');
Route::post('/get_user_list', 'ApiCommonController@getUserList')->middleware('auth');
Route::post('/get_user_list/csv', 'ApiCommonController@getUserListCsv')->middleware('auth');
Route::post('/get_user_shift', 'ApiCommonController@getShiftInformation')->middleware('auth');
// 締日取得
Route::post('/get_closing_day', 'ApiCommonController@getClosingDay')->middleware('auth');
// ユーザー権限取得
Route::post('/get_login_user_role', 'ApiCommonController@getLoginUserRole')->middleware('auth');
// ユーザー部署権限取得
Route::post('/get_login_user_department', 'ApiCommonController@getLoginUserDepartment')->middleware('auth');
// お知らせ取得
Route::get('/get_post_informations', 'ApiCommonController@getPostInformations')->middleware('auth');
Route::post('/insert_post_informations', 'ApiCommonController@insertPostInformations')->middleware('auth');
Route::post('/del_post_informations', 'ApiCommonController@delPostInformations')->middleware('auth');
// ユーザー所定時刻取得
Route::post('/get_working_hours', 'ApiCommonController@getWorkingHours')->middleware('auth');
// 勤務状況取得
Route::post('/get_working_status/get', 'ApiCommonController@getWorgingStatusInfo')->middleware('auth');
// CSV項目取得
Route::post('/get_csv_item', 'ApiCommonController@getCsvItem')->middleware('auth');


