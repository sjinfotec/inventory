<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkingTimeDatesTable1614 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('working_time_dates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('working_date')->comment('日付');
            $table->bigIncrements('department_id')->comment('部署コード');
            $table->String('department_name',8)->comment('部署名称');
            $table->char('user_code',10)->comment('ユーザー');
            $table->unsignedInteger('working_timetable_no')->comment('タイムテーブルNo');
            $table->String('working_timetable_name')->comment('タイムテーブル名称');
            $table->unsignedInteger('working_time_style')->comment('雇用形態');
            $table->String('working_time_style_name')->comment('雇用形態名称');
            $table->char('attendance_time',6)->nullable()->comment('出勤時刻');
            $table->char('leaving_time',6)->nullable()->comment('退勤時刻');
            $table->char('missing_middle_time',6)->nullable()->comment('中抜時刻');
            $table->char('missing_middle_return_time',6)->nullable()->comment('中抜戻り時刻');
            $table->double('regular_working_times',6,2)->nullable()->comment('所定労働時間');
            $table->double('out_of_regular_working_times',6,2)->nullable()->comment('所定外労働時間');
            $table->double('out_of_regular_night_working_times',6,2)->nullable()->comment('所定外深夜労働時間');
            $table->double('out_of_regular_total_working_times',6,2)->nullable()->comment('所定外合計労働時間');
            $table->double('not_employment_time',6,2)->nullable()->comment('不就労時間');
            $table->double('statutory_working_times',6,2)->nullable()->comment('法定労働時間');
            $table->double('out_of_statutory_working_times',6,2)->nullable()->comment('法定外労働時間');
            $table->unsignedInteger('leave_item_code')->nullable()->comment('休暇項目コード');
            $table->String('leave_item_name')->nullable()->comment('休暇項目名称');
            $table->char('fixedtime',1)->nullable()->comment('確定 1:確定');
            $table->String('created_user')->nullable()->comment('作成ユーザー');
            $table->String('updated_user')->nullable()->comment('修正ユーザー');
            $table->timestamps();
            $table->boolean('is_deleted')->default(0)->comment('削除フラグ');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('working_time_dates');
    }
}
