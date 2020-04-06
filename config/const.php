<?php

return [

    'C001' => [
        'value' => 'C001',
        'permanent_staff' => 1,
        'contract_staff' => 2,
        'part_timer' => 3,
        'permanent_part_timer' => 4,
        'seconded_staff' => 5,
        'temporary_staff' => 6
    ],

    'C002' => [
        'value' => 'C002',
        'legal_working_hours_day' => 8,
        'legal_working_hours_week' => 40
    ],

    // working_timetablesにはout_of_regular_night_working_timeまでのデータしかない
    'C004' => [
        'value' => 'C004',
        'regular_working_time' => 1,
        'regular_working_breaks_time' => 2,
        'out_of_regular_working_time' => 3,
        'out_of_regular_night_working_time' => 4,
        'statutory_working_time' => 5,
        'out_of_statutory_working_time' => 6,
        'out_of_statutory_night_working_time' => 7,
        'working_breaks_time' => 8
    ],

    'C005' => [
        'value' => 'C005',
        'attendance_time' => 1,
        'leaving_time' => 2,
        'missing_middle_time' => 11,
        'missing_middle_return_time' => 12,
        'public_going_out_time' => 21,
        'public_going_out_return_time' => 22
    ],

    'C006' => [
        'value' => 'C006',
        'sun' => 0,
        'mon' => 1,
        'tue' => 2,
        'wed' => 3,
        'thu' => 4,
        'fri' => 5,
        'sat' => 6
    ],

    'C007' => [
        'value' => 'C007',
        'basic' => '1',
        'legal_holoday' => '2',
        'legal_out_holoday' => '3'
    ],

    'C008' => [
        'value' => 'C008'
    ],

    'C009' => [
        'value' => 'C009',
        'round1' => 1,
        'round5' => 2,
        'round10' => 3,
        'round15' => 4,
        'round30' => 5,
        'round60' => 6
    ],

    'C010' => [
        'value' => 'C010',
        'round_half_up' => 1,
        'round_down' => 2,
        'round_up' => 3,
        'non' => 4
    ],

    'C012' => [
        'value' => 'C012',
        'attendance' => 1,
        'leaving' => 2,
        'missing_middle' => 3,
        'missing_middle_return' => 4,
        'public_going_out' => 5,
        'public_going_out_return' => 6,
        'user_holiday' => 7,
        'continue_work' => 10,
        'forget' => 90,
        'unknown' => 99
    ],

    'C013' => [
        'value' => 'C013',
        'non_set' => 0,
        'min_break_value' => 4,
        'paid_holiday' => 1,
        'morning_off' => 2,
        'afternoon_off' => 3,
        'substitute_holiday' => 4,
        'compensation_holiday' => 5,
        'summer_leave' => 6,
        'year_end_and_new_year_leave' => 7,
        'organization_anniversary' => 8,
        'prenatal_postnatal' => 9,
        'physiology_days_leave' => 10,
        'childcare_care_leave' => 11,
        'nursing_care_leave' => 12,
        'congratulatory_or_consolatory_leave' => 13,
        'refresh_leave' => 14,
        'absence_work' => 15,
        'late_work' => 16,
        'leave_early_work' => 17,
        'deemed_business_trip' => 18,
        'deemed_direct_go' => 19,
        'deemed_direct_return' => 20,
        'public_damage' => 21
    ],

    'C013_DESC_VALUE' => [
        'value' => 'C013',
        'non_set' => "",
        'target_calc_time' => "1日集計対象休暇",
        'half_am_' => "午前半休",
        'half_pm' => "午後半休",
        'non_calc_time' => "1日休暇",
        'late_work' => "遅刻",
        'early_work' => "早退",
        'deemed' => "みなし"
    ],

    'C013_DESC' => [
        'value' => 'C013',
        'non_set' => "",
        'paid_holiday' => "1日集計対象休暇",
        'morning_off' => "午前半休",
        'afternoon_off' => "午後半休",
        'substitute_holiday' => "1日休暇",
        'compensation_holiday' => "1日休暇",
        'summer_leave' => "1日休暇",
        'year_end_and_new_year_leave' => "1日休暇",
        'organization_anniversary' => "1日休暇",
        'prenatal_postnatal' => "1日休暇",
        'physiology_days_leave' => "1日休暇",
        'childcare_care_leave' => "1日休暇",
        'nursing_care_leave' => "1日休暇",
        'congratulatory_or_consolatory_leave' => "1日休暇",
        'refresh_leave' => "1日休暇",
        'absence_work' => "1日休暇",
        'late_work' => "遅刻",
        'leave_early_work' => "早退",
        'deemed_business_trip' => "みなし",
        'deemed_direct_go' => "みなし",
        'deemed_direct_return' => "みなし",
        'public_damage' => "1日集計対象休暇"
    ],

    'C014' => [
        'value' => 'C014',
        'fixed' => 1
    ],

    'C015' => [
        'value' => 'C015',
        'night_from' => '22:00:00',
        'night_to' => '05:00:00',
    ],

    'C016' => [
        'value' => 'C016',
        'display_closing' => '1',
        'display_month_start' => '2',
    ],
 
    'C017' => [
        'value' => 'C017',
        'target_user' => 1,
        'out_of_user' => 9,
        'admin_user' => 10
    ],
 
    'C018' => [
        'value' => 'C018',
        'normal_stamp' => 0,
        'forget_stamp' => 1,
        'interval_stamp' => 2,
        'no_leave_apply' => 3,
        'max_time_over' => 9
    ],
 
    'C018_NAME' => [
        'value' => 'C018',
        'normal_stamp' => '',
        'forget_stamp' => '前回打刻忘れの可能性',
        'interval_stamp' => '退勤と出勤時刻の時間が短い',
        'no_leave_apply' => '休暇未入力の可能性',
        'max_time_over' => '出退勤・外出制限回数オーバー'
    ],
 
    'C019' => [
        'value' => 'C019',
        'max_times' => 5
    ],
  
    'C021' => [
        'value' => 'C021',
        'manthly_alert_error_1' => 45,
        'manthly_alert_error_2' => 81,
        'manthly_alert_error_3' => 120,
        'manthly_alert_error_4' => 360,
        'manthly_alert_error_5' => 6,
        'manthly_alert_error_6' => 100,
        'manthly_alert_error_7' => 80,
        'manthly_alert_error_8' => 720
    ],

    'C022' => [
        'value' => 'C022',
        'monthly_alert_begining_month_closing' => 1,
        'monthly_alert_begining_month_first' => 2,
        'monthly_alert_first_month_closing' => 3,
        'monthly_alert_first_month_first' => 4
    ],
 
    'C023' => [
        'value' => 'C023',
        'manthly_alert_warning_1' => 40,
        'manthly_alert_warning_2' => 72,
        'manthly_alert_warning_3' => 108,
        'manthly_alert_warning_4' => 324,
        'manthly_alert_warning_5' => 5,
        'manthly_alert_warning_6' => 80,
        'manthly_alert_warning_7' => 72,
        'manthly_alert_warning_8' => 648,
        'manthly_alert_warning_9' => 45
    ],
 
    'C024' => [
        'value' => 'C024',
        'closing' => 1,
        'first' => 2
    ],
 
    'C025' => [
        'value' => 'C025',
        'general_user' => 1,
        'general_approver__user' => 5,
        'admin_user' => 9
    ],
 
    'C026' => [
        'value' => 'C026',
        'overtime_demand' => 1,
        'holidaywork_demand' => 2,
        'holidaytransfer_demand' => 3,
        'submission_demand' => 4,
        'shiftchange_demand' => 5,
        'paidholiday_demand' => 6,
        'late_demand' => 7,
        'earlyleave_demand' => 8,
        'goingout_demand' => 9,
        'absence_demand' => 10
    ],
 
    'C026_NAME' => [
        'value' => 'C026_NAME',
        'overtime_demand' => "残業申請",
        'holidaywork_demand' => "休日出勤申請",
        'holidaytransfer_demand' => "休日振替申請",
        'submission_demand' => "代休申請",
        'shiftchange_demand' => "シフト変更申請",
        'paidholiday_demand' => "有給休暇申請",
        'late_demand' => "遅刻申請",
        'earlyleave_demand' => "早退申請",
        'goingout_demand' => "外出申請書",
        'absence_demand' => "欠勤申請"
    ],
 
    'C027' => [
        'value' => 'C027',
        'main' => 1,
        'sub' => 2
    ],
 
    'C028' => [
        'value' => 'C028',
        'making' => '10',
        'applying' => '20',
        'approving' => '30',
        'send_back' => '40',
        'final_approved' => '50',
        'unknown' => '90',
        'breaking' => '91'
    ],
 
    'C029' => [
        'value' => 'C029',
        'before' => 1,
        'after' => 2
    ],
 
    'C030' => [
        'value' => 'C030',
        'already' => 1,
        'notyet' => 2
    ],
 
    'C031' => [
        'value' => 'C031',
        'approval_requesting' => 1,
        'approvaled' => 2
    ],
 
    'C033' => [
        'value' => 'C033',
        'pcstart' => 6005,
        'pcend' => 6006,
        'logon' => 7001,
        'logout' => 7002
    ],
 
    'C034' => [
        'value' => 'C034',
        'closing' => 1,
        'first' => 2
    ],
 
    'C999' => [
        'value' => 'C999',
        'main' => 1,
        'sub' => 2
    ],

    'WEEK_KANJI' => [
        'sun' => '(日)',
        'mon' => '(月)',
        'tue' => '(火)',
        'wed' => '(水)',
        'thu' => '(木)',
        'fri' => '(金)',
        'sat' => '(土)'
    ],

    'WORKING_TIME_NAME' => [
        'basic' => '労働合計時間',
        'legal_holoday' => '労働合計時間',
        'legal_out_holoday' => '労働合計時間'
    ],

    'PREDETER_TIME_NAME' => [
        'basic' => '残業時間',
        'legal_holoday' => '法定休日労働時間',
        'legal_out_holoday' => '法定外休日労働時間'
    ],

    'PREDETER_TIME_SECONDNAME' => [
        'basic' => '残業',
        'legal_holoday' => '法定休',
        'legal_out_holoday' => '法外休'
    ],

    'PREDETER_NIGHT_TIME_NAME' => [
        'basic' => '深夜残業時間',
        'legal_holoday' => '法定休日深夜時間',
        'legal_out_holoday' => '法定外休日深夜時間'
    ],

    'PREDETER_NIGHT_TIME_SECONDNAME' => [
        'basic' => '深夜残業',
        'legal_holoday' => '法休深夜',
        'legal_out_holoday' => '法外休深夜'
    ],

    'ARRAY_MAX_INDEX' => [
        'attendace_time' => 5,
        'leaving_time' => 5,
        'missing_middle_time' => 5,
        'missing_middle_return_time' => 5,
        'public_going_out_time' => 5,
        'public_going_out_return_time' => 5
    ],

    'SHOW_OR_UPDATE' => [
        'show' => 'show',
        'update' => 'update'
    ],

    'USER_PART' => [
        'alldepartment' => 'ALLDEP99',
        'alluser' => 'ALLUSER999'
    ],

    'TIME_TABLE_NO' => [
        'basic_no' => 1
    ],

    'INC_NO' => [
        'attendace_leaving' => 1,
        'missing_return' => 2,
        'public_going_out_return' => 3
    ],

    'REMARKS_DATA' => [
        'late' => '遅刻',
        'leaveearly' => '早退'
    ],

    'CONFIRM_SEQ' => [
        'not_final_confirm' => '0',
        'final_confirm' => '99'
    ],

    'DEMAND_KBN' => [
        'store' => 'store',
        'discharge' => 'discharge',
        'sendback' => 'sendback'
    ],

    'DB_KBN' => [
        'store' => 'store',
        'fix' => 'fix',
        'del' => 'del'
    ],

    'INIT_DATE' => [
        'initdate' => '20190101',
        'maxdate' => '20991231'
    ],

    'CALENDAR_PTN' => [
        'ptn1' => 1,
        'ptn2' => 2
    ],


    'MEMO_DATA' => [
        'MEMO_DATA_001' => '退勤打刻がない。',
        'MEMO_DATA_002' => '当日出勤なし。',
        'MEMO_DATA_003' => '外出状態。',
        'MEMO_DATA_004' => '不明な打刻。',
        'MEMO_DATA_005' => '当日時間計算なし。',
        'MEMO_DATA_006' => '自動設定。',
        'MEMO_DATA_007' => '退勤と出勤までの時間が不足。',
        'MEMO_DATA_008' => '休暇または欠勤入力要。',
        'MEMO_DATA_009' => '外出なし。',
        'MEMO_DATA_010' => '部署設定ミス。',
        'MEMO_DATA_011' => '締日設定ミス。',
        'MEMO_DATA_012' => '時間単位設定ミス。',
        'MEMO_DATA_013' => '時間端数処理設定ミス。',
        'MEMO_DATA_014' => '期首月設定ミス。',
        'MEMO_DATA_015' => '未設定。',
        'MEMO_DATA_016' => '出退勤打刻は１日最大５回まで。',
        'MEMO_DATA_017' => '公用外出打刻は１日最大５回まで。',
        'MEMO_DATA_018' => '私用外出打刻は１日最大５回まで。',
        'MEMO_DATA_NON' => ''
    ],
 
    'POST_ITEM' => [
        'card_id' => 'card_id',
        'mode' => 'mode'
    ],
 
    'PUT_ITEM' => [
        'result' => 'result',
        'listresult' => 'listresult',
        'user_code' => 'user_code',
        'user_name' => 'user_name',
        'department_code' => 'department_code',
        'department_name' => 'department_name',
        'record_time' => 'record_time',
        'source_mode' => 'source_mode'
    ],
 
    'RESULT_CODE' => [
        'normal' => 0,
        'success' => 0,
        'failed' => 1,
        'user_not_exsits' => 2,
        'card_not_exsits' => 3,
        'mode_illegal' => 4,
        'insert_error' => 5,
        'already_data' => 6,
        'select_error' => 7,
        'interval_stamp' => 8,
        'max_time_over' => 9,
        'dup_time_check' => 10,
        'other' => 99
    ],
 
    'RESPONCE_ITEM' => [
        'messagedata' => 'messagedata',
        'message' => 'message'
    ],
 
    'WORKINGTIME_DAY_OR_MONTH' => [
        'daily_basic' => '1',
        'monthly_basic' => '2'
    ],
 
    'ALERT_MONTHLY_ITEM' => [
        'items_1' => '労働時間の上限規制：月45時間（休日労働含めない）',
        'items_2' => '労働時間の上限規制：2ヶ月81時間（休日労働含めない）',
        'items_3' => '労働時間の上限規制：3ヶ月120時間（休日労働含めない）',
        'items_4' => '労働時間の上限規制：年360時間（休日労働含めない）',
        'items_5' => '特別条項の上限規制：年間6ヶ月（休日労働含めない）',
        'items_6' => '特別条項の上限規制：月100時間未満（休日労働含む）',
        'items_7' => '特別条項の上限規制：2ヶ月平均80時間（休日労働含む）',
        'items_8' => '特別条項の上限規制：3ヶ月平均80時間（休日労働含む）',
        'items_9' => '特別条項の上限規制：4ヶ月平均80時間（休日労働含む）',
        'items_10' => '特別条項の上限規制：5ヶ月平均80時間（休日労働含む）',
        'items_11' => '特別条項の上限規制：6ヶ月平均80時間（休日労働含む）',
        'items_12' => '特別条項の上限規制：年720時間（休日労働含めない）'
    ],
 
    'ALERT_INFO_RESULT' => [
        'OK' => 'OK',
        'WA' => 'WA',
        'NG' => 'NG'
    ],
 
    'ALERT_INFO_RESULT_NAME' => [
        'OK' => '正常',
        'WA' => '警告',
        'NG' => 'エラー'
    ],
 
    'FILE_DOWNLOAD_PATH' => [
        'STORAGE' => 'private/'
    ],
 
    'FILE_DOWNLOAD_NAME' => [
        'file1' => 'croud_install.zip',
        'file2' => 'pceventlog.zip',
        'file3' => 'TeamViewer_Setup.exe',
        'file4' => 'users_template_org.xlsx',
        'file5' => '',
        'file6' => '',
        'file7' => '',
        'file8' => '',
        'file9' => '',
        'file10' => '',
        'file11' => 'csd1000l_admin.xlsx',
        'file12' => 'csd1000l_admin.pdf',
        'file13' => 'Operationmanual(clientServer).docx',
        'file14' => 'Operationmanual(clientServer).pdf',
        'file15' => '',
        'file16' => '',
        'file17' => '',
        'file18' => '',
        'file19' => '',
        'file20' => '',
        'file21' => 'Installmanual.xlsx',
        'file22' => 'Installmanual.pdf',
        'file23' => 'Operationmanual(card_reader).xlsx',
        'file24' => 'Operationmanual(card_reader).pdf',
        'file25' => 'Operationmanual(setting).xlsx',
        'file26' => 'Operationmanual(setting).pdf',
        'file27' => 'Operationmanual(yayoi).xlsx',
        'file28' => 'Operationmanual(yayoi).pdf',
        'file29' => 'pceventlogmanual.xlsx',
        'file30' => 'pceventlogmanual.pdf',
        'file31' => 'Operationmanual(TeamViewer).xlsx',
        'file32' => 'Operationmanual(TeamViewer).pdf',
        'file33' => '',
        'file34' => '',
        'file35' => '',
        'file36' => '',
        'file37' => '',
        'file38' => '',
        'file39' => '',
        'file40' => ''
    ],

    'MSG_INFO' => [
        'past_closing' => '締日を過ぎていますが、集計しますか？',
        'past_closing_already' => '締日を過ぎていて集計済みですが、再集計しますか？',
        'no_alert_data' => '警告に該当する打刻内容はありませんでした。',
        'no_monthly_alert_data' => '警告に該当する内容はありませんでした。',
        'no_confirm_data' => '登録している承認者はありませんでした。新規に登録してください。',
        'no_data' => '該当する表示情報はありませんでした。'
    ],

    'MSG_ERROR' => [
        'not_workintime' => '期間内に該当する勤務時間は見つかりませんでした。',
        'not_between_workindate' => '計算開始日付が計算終了日付より未来の日付になっています。',
        'not_input_workindatefrom' => '計算開始日付は必ず入力してください。',
        'not_input_workindateto' => '計算終了日付は必ず入力してください。',
        'not_input_workindatefromto' => '計算開始日付と計算終了日付は必ず入力してください。',
        'not_setting_department_code' => '{0}さんの部署が設定されていません。',
        'not_setting_department_code_nouser' => '部署が設定されていない社員がいます。確認してください。',
        'not_setting_fiscal_month' => '月の時間計算設定が設定されていません。',
        'not_setting_closing' => '締日が設定されていません。',
        'not_setting_role' => 'ユーザー権限が設定されていません。',
        'not_setting_time_unit' => '時間計算の単位が設定されていません。',
        'not_setting_time_rounding' => '時間計算の端数処理が設定されていません。',
        'not_setting_beginning_month' => '期首月が設定されていません。',
        'not_setting_timetable' => '期間内に該当する勤務時間がないかまたはタイムテーブルが設定されていません。',
        'data_eror_dailycalc' => '労働時間計算処理にてエラーのため計算できませんでした。',
        'not_setting_calendar' => '{0}のカレンダー設定がされていません。',
        'data_accesee_eror_dailycalc' => '労働時間計算処理にてデータアクセスエラーのため計算できませんでした。',
        'data_accesee_eror' => 'データアクセスエラーのため取得できませんでした。',
        'data_insert_error' => '登録処理に失敗しました。',
        'mismatch_data' => 'データ不整合',
        'rounding_not_demand' => '承認回覧中のため、申請できません。',
        'making_or_final' => '取り下げまたは最終承認済みのため、承認できません。',
        'alreadymaking_or_final' => 'すでに取り下げているかまたは最終承認済みのため、取り下げできません。',
        'mail_send_eror' => 'メール送信異常のため送信できませんでした。',
        'already_data' => 'すでに登録されています',
        'already_name' => 'すでに登録されている[{0}]名です',
        'already_item' => 'すでに登録されている[{0}]です',
        'not_found_data' => '該当するデータは見つかりませんでした。',
        'parameter_illegal' => '不正なパラメータです。',
        'file_download_error' => 'ファイルダウンロード失敗しました。'
    ],

    'LOG_MSG' => [
        'data_eror_dailycalc' => 'data_error_dailycalc not calc',
        'data_select_erorr' => 'データselectエラー table = [{0}]',
        'data_insert_erorr' => 'データinsertエラー table = [{0}]',
        'data_update_erorr' => 'データupdateエラー table = [{0}]',
        'data_delete_erorr' => 'データdeleteエラー table = [{0}]',
        'data_exists_erorr' => 'データexistsエラー table = [{0}]',
        'data_maxget_erorr' => 'データmaxgetエラー table = [{0}]',
        'data_count_erorr' => 'データcountエラー table = [{0}]',
        'unknown_error' => '不明なエラー',
        'mismatch_data' => 'データ不整合',
        'not_set_time_rounding ' => '時間の端数処理が設定されていない',
        'not_setting_role' => 'ユーザー権限が設定されていない。user = [{0}]',
        'not_setting_timetable' => '期間内に該当する勤務時間がないかまたはタイムテーブルが設定されていない。',
        'non_approval_demandno' => '承認する申請番号なし。',
        'non_approval_demanddate' => '承認する申請日なし。',
        'subquery_illegal ' => 'subquery設定エラー',
        'where_illegal ' => 'WHERE句設定エラー',
        'parameter_illegal' => '不正なパラメータ params = [{0}]',
        'file_download_error' => 'ファイルダウンロード失敗しました。'
    ],

    'DEBUG_LEVEL' => 'DEBUG',

    'DEBUG_LEVEL_VALUE' => [
        'NON' => 'NON',
        'DEBUG' => 'DEBUG'
    ],

    'DISTRIBUTION' => [
        'DISTRIBUTION' => 2
    ],

    'DISTRIBUTION_VALUE' => [
        '43z' => 1,
        'SSJJOO' => 2,
        'MARUTAKA' => 3
    ],

    'EDITION' => [
        'EDITION' => 4
    ],

    'EDITION_VALUE' => [
        'DEMO' => 1,
        'TRIAL' => 2,
        'CROUD' => 3,
        'SSJJOO' => 4,
        'CLIENT' => 5
    ]

];
