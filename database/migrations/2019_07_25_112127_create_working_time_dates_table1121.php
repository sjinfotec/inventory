<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkingTimeDatesTable1121 extends Migration
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
            $table->unsignedDecimal('working_status',2,0)->nullable()->comment('勤務状態');
            $table->String('note')->nullable()->comment('メモ');
            $table->char('late',1)->nullable()->comment('遅刻有無');
            $table->char('Leave_early',1)->nullable()->comment('早退有無');
            $table->char('current_calc',1)->nullable()->comment('当日計算有無');
            $table->char('to_be_confirmed',1)->nullable()->comment('要確認有無');
            $table->unsignedDecimal('weekday_kubun',1,0)->nullable()->comment('曜日区分');
            $table->String('weekday_name')->nullable()->comment('曜日名称');
            $table->unsignedDecimal('business_kubun',2,0)->nullable()->comment('営業日区分');
            $table->String('business_name')->nullable()->comment('営業日名称');
            $table->unsignedDecimal('holiday_kubun',2,0)->nullable()->comment('休暇区分');
            $table->String('holiday_name')->nullable()->comment('休暇名称');
            $table->unsignedDecimal('closing',2,0)->nullable()->comment('締日');
            $table->double('uplimit_time',6,2)->nullable()->comment('上限残業時間');
            $table->double('statutory_uplimit_time',6,2)->nullable()->comment('法定上限残業時間');
            $table->unsignedInteger('time_unit')->nullable()->comment('時間単位');
            $table->unsignedInteger('time_rounding')->nullable()->comment('時間の丸め');
            $table->double('max_3month_total',7,2)->nullable()->comment('３ヶ月累計');
            $table->double('max_6month_total',7,2)->nullable()->comment('６ヶ月累計');
            $table->double('max_12month_total',7,2)->nullable()->comment('１年間累計');
            $table->unsignedDecimal('beginning_month',2,0)->nullable()->comment('期首月');
            $table->double('working_interval',4,2)->nullable()->comment('勤務間インターバル');
            $table->char('year',4)->nullable()->comment('年');
            $table->char('pattern',2)->nullable()->comment('打刻パターン');
            $table->char('fixedtime',1)->nullable()->comment('確定 1:確定');
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
