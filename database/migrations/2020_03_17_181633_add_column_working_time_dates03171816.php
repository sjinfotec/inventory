<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnWorkingTimeDates03171816 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('working_time_dates', function (Blueprint $table) {
            $table->string('attendance_editor_user_name_5')->nullable()->after('attendance_editor_user_code_5')->comment('出勤編集ユーザー名');
            $table->string('attendance_editor_user_name_4')->nullable()->after('attendance_editor_user_code_5')->comment('出勤編集ユーザー名');
            $table->string('attendance_editor_user_name_3')->nullable()->after('attendance_editor_user_code_5')->comment('出勤編集ユーザー名');
            $table->string('attendance_editor_user_name_2')->nullable()->after('attendance_editor_user_code_5')->comment('出勤編集ユーザー名');
            $table->string('attendance_editor_user_name_1')->nullable()->after('attendance_editor_user_code_5')->comment('出勤編集ユーザー名');
            $table->string('attendance_editor_department_name_5')->nullable()->after('attendance_editor_department_code_5')->comment('出勤編集部署名');
            $table->string('attendance_editor_department_name_4')->nullable()->after('attendance_editor_department_code_5')->comment('出勤編集部署名');
            $table->string('attendance_editor_department_name_3')->nullable()->after('attendance_editor_department_code_5')->comment('出勤編集部署名');
            $table->string('attendance_editor_department_name_2')->nullable()->after('attendance_editor_department_code_5')->comment('出勤編集部署名');
            $table->string('attendance_editor_department_name_1')->nullable()->after('attendance_editor_department_code_5')->comment('出勤編集部署名');
            $table->string('leaving_editor_user_name_5')->nullable()->after('leaving_editor_user_code_5')->comment('退勤編集ユーザー名');
            $table->string('leaving_editor_user_name_4')->nullable()->after('leaving_editor_user_code_5')->comment('退勤編集ユーザー名');
            $table->string('leaving_editor_user_name_3')->nullable()->after('leaving_editor_user_code_5')->comment('退勤編集ユーザー名');
            $table->string('leaving_editor_user_name_2')->nullable()->after('leaving_editor_user_code_5')->comment('退勤編集ユーザー名');
            $table->string('leaving_editor_user_name_1')->nullable()->after('leaving_editor_user_code_5')->comment('退勤編集ユーザー名');
            $table->string('leaving_editor_department_name_5')->nullable()->after('leaving_editor_department_code_5')->comment('退勤編集部署名');
            $table->string('leaving_editor_department_name_4')->nullable()->after('leaving_editor_department_code_5')->comment('退勤編集部署名');
            $table->string('leaving_editor_department_name_3')->nullable()->after('leaving_editor_department_code_5')->comment('退勤編集部署名');
            $table->string('leaving_editor_department_name_2')->nullable()->after('leaving_editor_department_code_5')->comment('退勤編集部署名');
            $table->string('leaving_editor_department_name_1')->nullable()->after('leaving_editor_department_code_5')->comment('退勤編集部署名');
            $table->string('missing_editor_user_name_5')->nullable()->after('missing_editor_user_code_5')->comment('私用外出編集ユーザー名');
            $table->string('missing_editor_user_name_4')->nullable()->after('missing_editor_user_code_5')->comment('私用外出編集ユーザー名');
            $table->string('missing_editor_user_name_3')->nullable()->after('missing_editor_user_code_5')->comment('私用外出編集ユーザー名');
            $table->string('missing_editor_user_name_2')->nullable()->after('missing_editor_user_code_5')->comment('私用外出編集ユーザー名');
            $table->string('missing_editor_user_name_1')->nullable()->after('missing_editor_user_code_5')->comment('私用外出編集ユーザー名');
            $table->string('missing_editor_department_name_5')->nullable()->after('missing_editor_department_code_5')->comment('私用外出編集部署名');
            $table->string('missing_editor_department_name_4')->nullable()->after('missing_editor_department_code_5')->comment('私用外出編集部署名');
            $table->string('missing_editor_department_name_3')->nullable()->after('missing_editor_department_code_5')->comment('私用外出編集部署名');
            $table->string('missing_editor_department_name_2')->nullable()->after('missing_editor_department_code_5')->comment('私用外出編集部署名');
            $table->string('missing_editor_department_name_1')->nullable()->after('missing_editor_department_code_5')->comment('私用外出編集部署名');
            $table->string('missing_return_editor_user_name_5')->nullable()->after('missing_return_editor_user_code_5')->comment('私用外出戻り編集ユーザー名');
            $table->string('missing_return_editor_user_name_4')->nullable()->after('missing_return_editor_user_code_5')->comment('私用外出戻り編集ユーザー名');
            $table->string('missing_return_editor_user_name_3')->nullable()->after('missing_return_editor_user_code_5')->comment('私用外出戻り編集ユーザー名');
            $table->string('missing_return_editor_user_name_2')->nullable()->after('missing_return_editor_user_code_5')->comment('私用外出戻り編集ユーザー名');
            $table->string('missing_return_editor_user_name_1')->nullable()->after('missing_return_editor_user_code_5')->comment('私用外出戻り編集ユーザー名');
            $table->string('missing_return_editor_department_name_5')->nullable()->after('missing_return_editor_department_code_5')->comment('私用外出戻り編集部署名');
            $table->string('missing_return_editor_department_name_4')->nullable()->after('missing_return_editor_department_code_5')->comment('私用外出戻り編集部署名');
            $table->string('missing_return_editor_department_name_3')->nullable()->after('missing_return_editor_department_code_5')->comment('私用外出戻り編集部署名');
            $table->string('missing_return_editor_department_name_2')->nullable()->after('missing_return_editor_department_code_5')->comment('私用外出戻り編集部署名');
            $table->string('missing_return_editor_department_name_1')->nullable()->after('missing_return_editor_department_code_5')->comment('私用外出戻り編集部署名');
            $table->string('public_editor_user_name_5')->nullable()->after('public_editor_user_code_5')->comment('公用外出編集ユーザー名');
            $table->string('public_editor_user_name_4')->nullable()->after('public_editor_user_code_5')->comment('公用外出編集ユーザー名');
            $table->string('public_editor_user_name_3')->nullable()->after('public_editor_user_code_5')->comment('公用外出編集ユーザー名');
            $table->string('public_editor_user_name_2')->nullable()->after('public_editor_user_code_5')->comment('公用外出編集ユーザー名');
            $table->string('public_editor_user_name_1')->nullable()->after('public_editor_user_code_5')->comment('公用外出編集ユーザー名');
            $table->string('public_editor_department_name_5')->nullable()->after('public_editor_department_code_5')->comment('公用外出編集部署名');
            $table->string('public_editor_department_name_4')->nullable()->after('public_editor_department_code_5')->comment('公用外出編集部署名');
            $table->string('public_editor_department_name_3')->nullable()->after('public_editor_department_code_5')->comment('公用外出編集部署名');
            $table->string('public_editor_department_name_2')->nullable()->after('public_editor_department_code_5')->comment('公用外出編集部署名');
            $table->string('public_editor_department_name_1')->nullable()->after('public_editor_department_code_5')->comment('公用外出編集部署名');
            $table->string('public_return_editor_user_name_5')->nullable()->after('public_return_editor_user_code_5')->comment('公用外出戻り編集ユーザー名');
            $table->string('public_return_editor_user_name_4')->nullable()->after('public_return_editor_user_code_5')->comment('公用外出戻り編集ユーザー名');
            $table->string('public_return_editor_user_name_3')->nullable()->after('public_return_editor_user_code_5')->comment('公用外出戻り編集ユーザー名');
            $table->string('public_return_editor_user_name_2')->nullable()->after('public_return_editor_user_code_5')->comment('公用外出戻り編集ユーザー名');
            $table->string('public_return_editor_user_name_1')->nullable()->after('public_return_editor_user_code_5')->comment('公用外出戻り編集ユーザー名');
            $table->string('public_return_editor_department_name_5')->nullable()->after('public_return_editor_department_code_5')->comment('公用外出編集戻り部署名');
            $table->string('public_return_editor_department_name_4')->nullable()->after('public_return_editor_department_code_5')->comment('公用外出編集戻り部署名');
            $table->string('public_return_editor_department_name_3')->nullable()->after('public_return_editor_department_code_5')->comment('公用外出編集戻り部署名');
            $table->string('public_return_editor_department_name_2')->nullable()->after('public_return_editor_department_code_5')->comment('公用外出編集戻り部署名');
            $table->string('public_return_editor_department_name_1')->nullable()->after('public_return_editor_department_code_5')->comment('公用外出編集戻り部署名');
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
            $table->dropColumn('attendance_editor_user_nam_5');
            $table->dropColumn('attendance_editor_user_nam_4');
            $table->dropColumn('attendance_editor_user_nam_3');
            $table->dropColumn('attendance_editor_user_nam_2');
            $table->dropColumn('attendance_editor_user_nam_1');
            $table->dropColumn('attendance_editor_department_nam_5');
            $table->dropColumn('attendance_editor_department_nam_4');
            $table->dropColumn('attendance_editor_department_nam_3');
            $table->dropColumn('attendance_editor_department_nam_2');
            $table->dropColumn('attendance_editor_department_nam_1');
            $table->dropColumn('leaving_editor_user_nam_5');
            $table->dropColumn('leaving_editor_user_nam_4');
            $table->dropColumn('leaving_editor_user_nam_3');
            $table->dropColumn('leaving_editor_user_nam_2');
            $table->dropColumn('leaving_editor_user_nam_1');
            $table->dropColumn('leaving_editor_department_nam_5');
            $table->dropColumn('leaving_editor_department_nam_4');
            $table->dropColumn('leaving_editor_department_nam_3');
            $table->dropColumn('leaving_editor_department_nam_2');
            $table->dropColumn('leaving_editor_department_nam_1');
            $table->dropColumn('missing_editor_user_nam_5');
            $table->dropColumn('missing_editor_user_nam_4');
            $table->dropColumn('missing_editor_user_nam_3');
            $table->dropColumn('missing_editor_user_nam_2');
            $table->dropColumn('missing_editor_user_nam_1');
            $table->dropColumn('missing_editor_department_nam_5');
            $table->dropColumn('missing_editor_department_nam_4');
            $table->dropColumn('missing_editor_department_nam_3');
            $table->dropColumn('missing_editor_department_nam_2');
            $table->dropColumn('missing_editor_department_nam_1');
            $table->dropColumn('missing_return_editor_user_nam_5');
            $table->dropColumn('missing_return_editor_user_nam_4');
            $table->dropColumn('missing_return_editor_user_nam_3');
            $table->dropColumn('missing_return_editor_user_nam_2');
            $table->dropColumn('missing_return_editor_user_nam_1');
            $table->dropColumn('missing_return_editor_department_nam_5');
            $table->dropColumn('missing_return_editor_department_nam_4');
            $table->dropColumn('missing_return_editor_department_nam_3');
            $table->dropColumn('missing_return_editor_department_nam_2');
            $table->dropColumn('missing_return_editor_department_nam_1');
            $table->dropColumn('public_editor_user_nam_5');
            $table->dropColumn('public_editor_user_nam_4');
            $table->dropColumn('public_editor_user_nam_3');
            $table->dropColumn('public_editor_user_nam_2');
            $table->dropColumn('public_editor_user_nam_1');
            $table->dropColumn('public_editor_department_nam_5');
            $table->dropColumn('public_editor_department_nam_4');
            $table->dropColumn('public_editor_department_nam_3');
            $table->dropColumn('public_editor_department_nam_2');
            $table->dropColumn('public_editor_department_nam_1');
            $table->dropColumn('public_return_editor_user_nam_5');
            $table->dropColumn('public_return_editor_user_nam_4');
            $table->dropColumn('public_return_editor_user_nam_3');
            $table->dropColumn('public_return_editor_user_nam_2');
            $table->dropColumn('public_return_editor_user_nam_1');
            $table->dropColumn('public_return_editor_department_nam_5');
            $table->dropColumn('public_return_editor_department_nam_4');
            $table->dropColumn('public_return_editor_department_nam_3');
            $table->dropColumn('public_return_editor_department_nam_2');
            $table->dropColumn('public_return_editor_department_nam_1');
        });
    }
}
