<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnWorkingTimeDates03171649 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('working_time_dates', function (Blueprint $table) {
            $table->char('attendance_editor_user_code_5',10)->nullable()->after('attendance_time_id_5')->comment('出勤編集ユーザーコード');
            $table->char('attendance_editor_user_code_4',10)->nullable()->after('attendance_time_id_5')->comment('出勤編集ユーザーコード');
            $table->char('attendance_editor_user_code_3',10)->nullable()->after('attendance_time_id_5')->comment('出勤編集ユーザーコード');
            $table->char('attendance_editor_user_code_2',10)->nullable()->after('attendance_time_id_5')->comment('出勤編集ユーザーコード');
            $table->char('attendance_editor_user_code_1',10)->nullable()->after('attendance_time_id_5')->comment('出勤編集ユーザーコード');
            $table->char('attendance_editor_department_code_5',8)->nullable()->after('attendance_time_id_5')->comment('出勤編集部署コード');
            $table->char('attendance_editor_department_code_4',8)->nullable()->after('attendance_time_id_5')->comment('出勤編集部署コード');
            $table->char('attendance_editor_department_code_3',8)->nullable()->after('attendance_time_id_5')->comment('出勤編集部署コード');
            $table->char('attendance_editor_department_code_2',8)->nullable()->after('attendance_time_id_5')->comment('出勤編集部署コード');
            $table->char('attendance_editor_department_code_1',8)->nullable()->after('attendance_time_id_5')->comment('出勤編集部署コード');
            $table->char('leaving_editor_user_code_5',10)->nullable()->after('leaving_time_id_5')->comment('退勤編集ユーザーコード');
            $table->char('leaving_editor_user_code_4',10)->nullable()->after('leaving_time_id_5')->comment('退勤編集ユーザーコード');
            $table->char('leaving_editor_user_code_3',10)->nullable()->after('leaving_time_id_5')->comment('退勤編集ユーザーコード');
            $table->char('leaving_editor_user_code_2',10)->nullable()->after('leaving_time_id_5')->comment('退勤編集ユーザーコード');
            $table->char('leaving_editor_user_code_1',10)->nullable()->after('leaving_time_id_5')->comment('退勤編集ユーザーコード');
            $table->char('leaving_editor_department_code_5',8)->nullable()->after('leaving_time_id_5')->comment('退勤編集部署コード');
            $table->char('leaving_editor_department_code_4',8)->nullable()->after('leaving_time_id_5')->comment('退勤編集部署コード');
            $table->char('leaving_editor_department_code_3',8)->nullable()->after('leaving_time_id_5')->comment('退勤編集部署コード');
            $table->char('leaving_editor_department_code_2',8)->nullable()->after('leaving_time_id_5')->comment('退勤編集部署コード');
            $table->char('leaving_editor_department_code_1',8)->nullable()->after('leaving_time_id_5')->comment('退勤編集部署コード');
            $table->char('missing_editor_user_code_5',10)->nullable()->after('missing_middle_time_id_5')->comment('私用外出編集ユーザーコード');
            $table->char('missing_editor_user_code_4',10)->nullable()->after('missing_middle_time_id_5')->comment('私用外出編集ユーザーコード');
            $table->char('missing_editor_user_code_3',10)->nullable()->after('missing_middle_time_id_5')->comment('私用外出編集ユーザーコード');
            $table->char('missing_editor_user_code_2',10)->nullable()->after('missing_middle_time_id_5')->comment('私用外出編集ユーザーコード');
            $table->char('missing_editor_user_code_1',10)->nullable()->after('missing_middle_time_id_5')->comment('私用外出編集ユーザーコード');
            $table->char('missing_editor_department_code_5',8)->nullable()->after('missing_middle_time_id_5')->comment('私用外出編集部署コード');
            $table->char('missing_editor_department_code_4',8)->nullable()->after('missing_middle_time_id_5')->comment('私用外出編集部署コード');
            $table->char('missing_editor_department_code_3',8)->nullable()->after('missing_middle_time_id_5')->comment('私用外出編集部署コード');
            $table->char('missing_editor_department_code_2',8)->nullable()->after('missing_middle_time_id_5')->comment('私用外出編集部署コード');
            $table->char('missing_editor_department_code_1',8)->nullable()->after('missing_middle_time_id_5')->comment('私用外出編集部署コード');
            $table->char('missing_return_editor_user_code_5',10)->nullable()->after('missing_middle_return_time_id_5')->comment('私用外出戻り編集ユーザーコード');
            $table->char('missing_return_editor_user_code_4',10)->nullable()->after('missing_middle_return_time_id_5')->comment('私用外出戻り編集ユーザーコード');
            $table->char('missing_return_editor_user_code_3',10)->nullable()->after('missing_middle_return_time_id_5')->comment('私用外出戻り編集ユーザーコード');
            $table->char('missing_return_editor_user_code_2',10)->nullable()->after('missing_middle_return_time_id_5')->comment('私用外出戻り編集ユーザーコード');
            $table->char('missing_return_editor_user_code_1',10)->nullable()->after('missing_middle_return_time_id_5')->comment('私用外出戻り編集ユーザーコード');
            $table->char('missing_return_editor_department_code_5',8)->nullable()->after('missing_middle_return_time_id_5')->comment('私用外出戻り編集部署コード');
            $table->char('missing_return_editor_department_code_4',8)->nullable()->after('missing_middle_return_time_id_5')->comment('私用外出戻り編集部署コード');
            $table->char('missing_return_editor_department_code_3',8)->nullable()->after('missing_middle_return_time_id_5')->comment('私用外出戻り編集部署コード');
            $table->char('missing_return_editor_department_code_2',8)->nullable()->after('missing_middle_return_time_id_5')->comment('私用外出戻り編集部署コード');
            $table->char('missing_return_editor_department_code_1',8)->nullable()->after('missing_middle_return_time_id_5')->comment('私用外出戻り編集部署コード');
            $table->char('public_editor_user_code_5',10)->nullable()->after('public_going_out_time_id_5')->comment('公用外出編集ユーザーコード');
            $table->char('public_editor_user_code_4',10)->nullable()->after('public_going_out_time_id_5')->comment('公用外出編集ユーザーコード');
            $table->char('public_editor_user_code_3',10)->nullable()->after('public_going_out_time_id_5')->comment('公用外出編集ユーザーコード');
            $table->char('public_editor_user_code_2',10)->nullable()->after('public_going_out_time_id_5')->comment('公用外出編集ユーザーコード');
            $table->char('public_editor_user_code_1',10)->nullable()->after('public_going_out_time_id_5')->comment('公用外出編集ユーザーコード');
            $table->char('public_editor_department_code_5',8)->nullable()->after('public_going_out_time_id_5')->comment('公用外出編集部署コード');
            $table->char('public_editor_department_code_4',8)->nullable()->after('public_going_out_time_id_5')->comment('公用外出編集部署コード');
            $table->char('public_editor_department_code_3',8)->nullable()->after('public_going_out_time_id_5')->comment('公用外出編集部署コード');
            $table->char('public_editor_department_code_2',8)->nullable()->after('public_going_out_time_id_5')->comment('公用外出編集部署コード');
            $table->char('public_editor_department_code_1',8)->nullable()->after('public_going_out_time_id_5')->comment('公用外出編集部署コード');
            $table->char('public_return_editor_user_code_5',10)->nullable()->after('public_going_out_return_time_id_5')->comment('公用外出戻り編集ユーザーコード');
            $table->char('public_return_editor_user_code_4',10)->nullable()->after('public_going_out_return_time_id_5')->comment('公用外出戻り編集ユーザーコード');
            $table->char('public_return_editor_user_code_3',10)->nullable()->after('public_going_out_return_time_id_5')->comment('公用外出戻り編集ユーザーコード');
            $table->char('public_return_editor_user_code_2',10)->nullable()->after('public_going_out_return_time_id_5')->comment('公用外出戻り編集ユーザーコード');
            $table->char('public_return_editor_user_code_1',10)->nullable()->after('public_going_out_return_time_id_5')->comment('公用外出戻り編集ユーザーコード');
            $table->char('public_return_editor_department_code_5',8)->nullable()->after('public_going_out_return_time_id_5')->comment('公用外出編集戻り部署コード');
            $table->char('public_return_editor_department_code_4',8)->nullable()->after('public_going_out_return_time_id_5')->comment('公用外出編集戻り部署コード');
            $table->char('public_return_editor_department_code_3',8)->nullable()->after('public_going_out_return_time_id_5')->comment('公用外出編集戻り部署コード');
            $table->char('public_return_editor_department_code_2',8)->nullable()->after('public_going_out_return_time_id_5')->comment('公用外出編集戻り部署コード');
            $table->char('public_return_editor_department_code_1',8)->nullable()->after('public_going_out_return_time_id_5')->comment('公用外出編集戻り部署コード');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('working_time_dates', function (Blueprint $table) {
            $table->dropColumn('attendance_editor_user_code_5');
            $table->dropColumn('attendance_editor_user_code_4');
            $table->dropColumn('attendance_editor_user_code_3');
            $table->dropColumn('attendance_editor_user_code_2');
            $table->dropColumn('attendance_editor_user_code_1');
            $table->dropColumn('attendance_editor_department_code_5');
            $table->dropColumn('attendance_editor_department_code_4');
            $table->dropColumn('attendance_editor_department_code_3');
            $table->dropColumn('attendance_editor_department_code_2');
            $table->dropColumn('attendance_editor_department_code_1');
            $table->dropColumn('leaving_editor_user_code_5');
            $table->dropColumn('leaving_editor_user_code_4');
            $table->dropColumn('leaving_editor_user_code_3');
            $table->dropColumn('leaving_editor_user_code_2');
            $table->dropColumn('leaving_editor_user_code_1');
            $table->dropColumn('leaving_editor_department_code_5');
            $table->dropColumn('leaving_editor_department_code_4');
            $table->dropColumn('leaving_editor_department_code_3');
            $table->dropColumn('leaving_editor_department_code_2');
            $table->dropColumn('leaving_editor_department_code_1');
            $table->dropColumn('missing_editor_user_code_5');
            $table->dropColumn('missing_editor_user_code_4');
            $table->dropColumn('missing_editor_user_code_3');
            $table->dropColumn('missing_editor_user_code_2');
            $table->dropColumn('missing_editor_user_code_1');
            $table->dropColumn('missing_editor_department_code_5');
            $table->dropColumn('missing_editor_department_code_4');
            $table->dropColumn('missing_editor_department_code_3');
            $table->dropColumn('missing_editor_department_code_2');
            $table->dropColumn('missing_editor_department_code_1');
            $table->dropColumn('missing_return_editor_user_code_5');
            $table->dropColumn('missing_return_editor_user_code_4');
            $table->dropColumn('missing_return_editor_user_code_3');
            $table->dropColumn('missing_return_editor_user_code_2');
            $table->dropColumn('missing_return_editor_user_code_1');
            $table->dropColumn('missing_return_editor_department_code_5');
            $table->dropColumn('missing_return_editor_department_code_4');
            $table->dropColumn('missing_return_editor_department_code_3');
            $table->dropColumn('missing_return_editor_department_code_2');
            $table->dropColumn('missing_return_editor_department_code_1');
            $table->dropColumn('public_editor_user_code_5');
            $table->dropColumn('public_editor_user_code_4');
            $table->dropColumn('public_editor_user_code_3');
            $table->dropColumn('public_editor_user_code_2');
            $table->dropColumn('public_editor_user_code_1');
            $table->dropColumn('public_editor_department_code_5');
            $table->dropColumn('public_editor_department_code_4');
            $table->dropColumn('public_editor_department_code_3');
            $table->dropColumn('public_editor_department_code_2');
            $table->dropColumn('public_editor_department_code_1');
            $table->dropColumn('public_return_editor_user_code_5');
            $table->dropColumn('public_return_editor_user_code_4');
            $table->dropColumn('public_return_editor_user_code_3');
            $table->dropColumn('public_return_editor_user_code_2');
            $table->dropColumn('public_return_editor_user_code_1');
            $table->dropColumn('public_return_editor_department_code_5');
            $table->dropColumn('public_return_editor_department_code_4');
            $table->dropColumn('public_return_editor_department_code_3');
            $table->dropColumn('public_return_editor_department_code_2');
            $table->dropColumn('public_return_editor_department_code_1');
        });
    }
}
