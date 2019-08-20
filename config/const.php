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
        'missing_middle_return_time' => 12
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
        'value' => 'C007'
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
        'continue_work' => 5,
        'forget' => 9,
        'unknown' => 99
    ],

    'C013' => [
        'value' => 'C013',
        'min_break_value' => 1,
        'paid_leave' => 1,
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
        'max_break_value' => 14
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

    'C999' => [
        'value' => 'C999',
        'init_setting_code' => '9999',
        'init_setting_user' => 'systemuser'
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

    'ARRAY_MAX_INDEX' => [
        'attendace_time' => 5,
        'leaving_time' => 5,
        'missing_middle_time' => 5,
        'missing_middle_return_time' => 5
    ],

    'TIME_TABLE_NO' => [
        'basic_no' => 1
    ],

    'INC_NO' => [
        'attendace_leaving' => 1,
        'missing_return' => 2
    ],


    'MEMO_DATA' => [
        'MEMO_DATA_001' => '出勤済',
        'MEMO_DATA_002' => '当日出勤なし',
        'MEMO_DATA_003' => '中抜け状態',
        'MEMO_DATA_004' => '不明な打刻',
        'MEMO_DATA_005' => '当日時間計算なし',
        'MEMO_DATA_006' => '自動設定',
        'MEMO_DATA_007' => '勤務間インターバルオーバー',
        'MEMO_DATA_008' => '未出勤',
        'MEMO_DATA_009' => '中抜けなし',
        'MEMO_DATA_010' => '部署設定ミス',
        'MEMO_DATA_011' => '締日設定ミス',
        'MEMO_DATA_012' => '時間単位設定ミス',
        'MEMO_DATA_013' => '時間端数処理設定ミス',
        'MEMO_DATA_014' => '期首月設定ミス',
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
        'other' => 99
    ],
 
    'RESPONCE_ITEM' => [
        'messagedata' => 'messagedata'
    ],
 
    'WORKINGTIME_ORDERBY' => [
        'daily_basic' => '1',
        'monthly_basic' => '2'
    ],

    'MSG_WARNING' => [
        'past_closing' => '締日を過ぎていますが、集計しますか？',
        'past_closing_already' => '締日を過ぎていて集計済みですが、再集計しますか？'
    ],

    'MSG_ERROR' => [
        'not_workintime' => '期間内に該当する勤務時間は見つかりませんでした。',
        'not_between_workindate' => '計算開始日付が計算終了日付より未来の日付になっています。',
        'not_input_workindatefrom' => '計算開始日付は必ず入力してください。',
        'not_input_workindateto' => '計算終了日付は必ず入力してください。',
        'not_input_workindatefromto' => '計算開始日付と計算終了日付は必ず入力してください。',
        'not_setting_department_code' => 'さんの部署が設定されていません。',
        'not_setting_department_code_nouser' => '部署が設定されていない社員がいます。確認してください。',
        'not_setting_closing' => '締日が設定されていません。',
        'not_setting_time_unit' => '時間計算の単位が設定されていません。',
        'not_setting_time_rounding' => '時間計算の端数処理が設定されていません。',
        'not_setting_beginning_month' => '期首月が設定されていません。',
        'not_setting_timetable' => 'タイムテーブルが設定されていません。',
        'data_eror_dailycalc' => '労働時間計算処理にてエラーのため計算できませんでした。',
        'data_accesee_eror_dailycalc' => '労働時間計算処理にてデータアクセスエラーのため計算できませんでした。',
        'mismatch_data' => 'データ不整合'
    ],

    'LOG_MSG' => [
        'data_eror_dailycalc' => 'data_error_dailycalc not calc',
        'data_select_erorr' => 'データselectエラー table = [{0}]',
        'data_insert_erorr' => 'データinsertエラー table = [{0}]',
        'data_delete_erorr' => 'データdeleteエラー table = [{0}]',
        'data_exists_erorr' => 'データexistsエラー table = [{0}]',
        'mismatch_data' => 'データ不整合',
        'not_set_time_rounding ' => '時間の端数処理が設定されていない'
    ]

];
