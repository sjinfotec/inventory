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
        'value' => 'C006'
    ],

    'C007' => [
        'value' => 'C007'
    ],

    'C008' => [
        'value' => 'C008'
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
        'MEMO_DATA_NON' => ''
    ],

    'MSG_ERROR' => [
        'not_workintime' => '期間内に該当する勤務時間は見つかりませんでした。',
        'not_between_workindate' => '計算開始日付が計算終了日付より未来の日付になっています。',
        'not_input_workindatefrom' => '計算開始日付は必ず入力してください。',
        'not_input_workindateto' => '計算終了日付は必ず入力してください。',
        'not_input_workindatefromto' => '計算開始日付と計算終了日付は必ず入力してください。',
        'not_setting_department_id' => 'さんの部署が設定されていません。',
        'not_setting_department_id_nouser' => '部署が設定されていない社員がいます。確認してください。',
        'not_setting_closing' => '締日が設定されていません。',
        'not_setting_time_unit' => '時間計算の単位が設定されていません。',
        'not_setting_time_rounding' => '時間計算の端数処理（丸め）が設定されていません。',
        'not_setting_beginning_month' => '期首月が設定されていません。',
        'mismatch_data' => 'データ不整合'
    ]

];
