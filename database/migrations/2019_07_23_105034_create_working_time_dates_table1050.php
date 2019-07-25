<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkingTimeDatesTable1050 extends Migration
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
            $table->char('working_date',8)->comment('日付');
            $table->unsignedInteger('employment_status')->comment('雇用形態');
            $table->char('department_id',8)->comment('部署コード');
            $table->char('user_code',10)->comment('ユーザー');
            $table->String('working_time_style_name')->nullable()->comment('雇用形態名称');
            $table->String('department_name')->nullable()->comment('部署名称');
            $table->String('user_name')->nullable()->comment('ユーザー名称');
            $table->unsignedInteger('working_timetable_no')->nullable()->comment('タイムテーブルNo');
            $table->String('working_timetable_name')->nullable()->comment('タイムテーブル名称');
            $table->double('total_working_times',6,2)->nullable()->comment('合計勤務時間');
            $table->double('regular_working_times',6,2)->nullable()->comment('所定労働時間');
            $table->double('out_of_regular_working_times',6,2)->nullable()->comment('所定外労働時間');
            $table->double('overtime_hours',6,2)->nullable()->comment('残業時間');
            $table->double('late_night_overtime_hours',6,2)->nullable()->comment('深夜残業時間');
            $table->double('legal_working_times',6,2)->nullable()->comment('法定労働時間');
            $table->double('out_of_legal_working_times',6,2)->nullable()->comment('法定外労働時間');
            $table->double('not_employment_working_hours',6,2)->nullable()->comment('未就労労働時間');
            $table->double('off_hours_working_hours',6,2)->nullable()->comment('時間外労働時間');
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
