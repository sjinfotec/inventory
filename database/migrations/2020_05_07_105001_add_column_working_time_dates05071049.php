<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnWorkingTimeDates05071049 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('working_time_dates', function (Blueprint $table) {
            $table->dateTime('public_going_out_time_7')->nullable()->after('public_going_out_time_5')->comment('公用外出時刻７');
            $table->dateTime('public_going_out_time_6')->nullable()->after('public_going_out_time_5')->comment('公用外出時刻６');
            $table->geometry('public_going_out_time_positions_7')->nullable()->after('public_going_out_time_positions_5')->comment('公用外出位置情報７');
            $table->geometry('public_going_out_time_positions_6')->nullable()->after('public_going_out_time_positions_5')->comment('公用外出位置情報６');
            $table->bigInteger('public_going_out_time_id_7')->nullable()->after('public_going_out_time_id_5')->comment('公用外出打刻時刻テーブルID7');
            $table->bigInteger('public_going_out_time_id_6')->nullable()->after('public_going_out_time_id_5')->comment('公用外出打刻時刻テーブルID6');
            $table->char('public_editor_department_code_7', 8)->nullable()->after('public_editor_department_code_5')->comment('公用外出編集部署コード７');
            $table->char('public_editor_department_code_6', 8)->nullable()->after('public_editor_department_code_5')->comment('公用外出編集部署コード６');
            $table->text('public_editor_department_name_7')->nullable()->after('public_editor_department_name_5')->comment('公用外出編集部署名７');
            $table->text('public_editor_department_name_6')->nullable()->after('public_editor_department_name_5')->comment('公用外出編集部署名６');
            $table->char('public_editor_user_code_7', 10)->nullable()->after('public_editor_user_code_5')->comment('公用外出編集ユーザーコード７');
            $table->char('public_editor_user_code_6', 10)->nullable()->after('public_editor_user_code_5')->comment('公用外出編集ユーザーコード６');
            $table->text('public_editor_user_name_7')->nullable()->after('public_editor_user_name_5')->comment('公用外出編集ユーザー名７');
            $table->text('public_editor_user_name_6')->nullable()->after('public_editor_user_name_5')->comment('公用外出編集ユーザー名６');
            $table->dateTime('public_going_out_return_time_7')->nullable()->after('public_going_out_return_time_5')->comment('公用外出戻り時刻７');
            $table->dateTime('public_going_out_return_time_6')->nullable()->after('public_going_out_return_time_5')->comment('公用外出戻り時刻６');
            $table->geometry('public_going_out_return_time_positions_7')->nullable()->after('public_going_out_return_time_positions_5')->comment('公用外出戻り位置情報７');
            $table->geometry('public_going_out_return_time_positions_6')->nullable()->after('public_going_out_return_time_positions_5')->comment('公用外出戻り位置情報６');
            $table->bigInteger('public_going_out_return_time_id_7')->nullable()->after('public_going_out_return_time_id_5')->comment('公用外出戻り打刻時刻テーブルID7');
            $table->bigInteger('public_going_out_return_time_id_6')->nullable()->after('public_going_out_return_time_id_5')->comment('公用外出戻り打刻時刻テーブルID6');
            $table->char('public_return_editor_department_code_7', 8)->nullable()->after('public_return_editor_department_code_5')->comment('公用外出編集戻り部署コード７');
            $table->char('public_return_editor_department_code_6', 8)->nullable()->after('public_return_editor_department_code_5')->comment('公用外出編集戻り部署コード６');
            $table->text('public_return_editor_department_name_7')->nullable()->after('public_return_editor_department_name_5')->comment('公用外出編集戻り部署名７');
            $table->text('public_return_editor_department_name_6')->nullable()->after('public_return_editor_department_name_5')->comment('公用外出編集戻り部署名６');
            $table->char('public_return_editor_user_code_7', 10)->nullable()->after('public_return_editor_user_code_5')->comment('公用外出戻り編集ユーザーコード７');
            $table->char('public_return_editor_user_code_6', 10)->nullable()->after('public_return_editor_user_code_5')->comment('公用外出戻り編集ユーザーコード６');
            $table->text('public_return_editor_user_name_7')->nullable()->after('public_return_editor_user_name_5')->comment('公用外出戻り編集ユーザー名７');
            $table->text('public_return_editor_user_name_6')->nullable()->after('public_return_editor_user_name_5')->comment('公用外出戻り編集ユーザー名６');
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
            $table->dropColumn('public_going_out_time_7');
            $table->dropColumn('public_going_out_time_6');
            $table->dropColumn('public_going_out_time_positions_7');
            $table->dropColumn('public_going_out_time_positions_6');
            $table->dropColumn('public_going_out_time_id_7');
            $table->dropColumn('public_going_out_time_id_6');
            $table->dropColumn('public_editor_department_code_7');
            $table->dropColumn('public_editor_department_code_6');
            $table->dropColumn('public_editor_department_name_9');
            $table->dropColumn('public_editor_department_name_7');
            $table->dropColumn('public_editor_department_name_6');
            $table->dropColumn('public_editor_user_code_7');
            $table->dropColumn('public_editor_user_code_6');
            $table->dropColumn('public_editor_user_name_7');
            $table->dropColumn('public_editor_user_name_6');
            $table->dropColumn('public_going_out_return_time_7');
            $table->dropColumn('public_going_out_return_time_6');
            $table->dropColumn('public_going_out_return_time_positions_7');
            $table->dropColumn('public_going_out_return_time_positions_6');
            $table->dropColumn('public_going_out_return_time_id_7');
            $table->dropColumn('public_going_out_return_time_id_6');
            $table->dropColumn('public_return_editor_department_code_7');
            $table->dropColumn('public_return_editor_department_code_6');
            $table->dropColumn('public_return_editor_department_name_7');
            $table->dropColumn('public_return_editor_department_name_6');
            $table->dropColumn('public_return_editor_user_code_7');
            $table->dropColumn('public_return_editor_user_code_6');
            $table->dropColumn('public_return_editor_user_name_7');
            $table->dropColumn('public_return_editor_user_name_6');
        });
    }
}
