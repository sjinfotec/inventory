<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempCalcWorkingtimesTable1521 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_calc_workingtimes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('working_date')->comment('日付');
            $table->unsignedDecimal('employment_status',2,0)->comment('雇用形態');
            $table->bigInteger('department_id')->comment('部署ID');
            $table->char('user_code',10)->comment('ユーザー');
            $table->String('employment_status_name')->nullable()->comment('雇用形態名称');
            $table->String('department_name')->nullable()->comment('部署名称');
            $table->String('user_name')->nullable()->comment('ユーザー名称');
            $table->unsignedInteger('working_timetable_no')->nullable()->comment('タイムテーブルNo');
            $table->String('working_timetable_name')->nullable()->comment('タイムテーブル名称');
            $table->time('working_timetable_from_time')->nullable()->comment('タイムテーブル開始時刻');
            $table->time('working_timetable_to_time')->nullable()->comment('タイムテーブル終了時刻');
            $table->unsignedInteger('shift_no')->nullable()->comment('シフトタイムテーブルNo');
            $table->String('shift_name')->nullable()->comment('シフトタイムテーブル名称');
            $table->time('shift_from_time')->nullable()->comment('シフトタイムテーブル開始時刻');
            $table->time('shift_to_time')->nullable()->comment('シフトタイムテーブル終了時刻');
            $table->unsignedDecimal('mode',2,0)->nullable()->comment('打刻モード');
            $table->datetime('record_datetime')->nullable()->comment('打刻日時');
            $table->char('record_year',4)->nullable()->comment('打刻年');
            $table->char('record_month',2)->nullable()->comment('打刻月');
            $table->char('record_date',8)->nullable()->comment('打刻年月日');
            $table->char('record_time',6)->nullable()->comment('打刻時刻');
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
        Schema::dropIfExists('temp_calc_workingtimes');
    }
}
