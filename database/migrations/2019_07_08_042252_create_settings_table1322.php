<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable1322 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('no')->comment('設定No');
            $table->unsignedInteger('time_unit')->comment('時間単位');
            $table->unsignedInteger('time_rounding')->comment('時間の丸め');
            $table->double('uplimit_time_1month',6,2)->comment('1月上限残業時間');
            $table->double('uplimit_time_2month',6,2)->comment('2月上限残業時間');
            $table->double('uplimit_time_3month',6,2)->comment('3月上限残業時間');
            $table->double('uplimit_time_4month',6,2)->comment('4月上限残業時間');
            $table->double('uplimit_time_5month',6,2)->comment('5月上限残業時間');
            $table->double('uplimit_time_6month',6,2)->comment('6月上限残業時間');
            $table->double('uplimit_time_7month',6,2)->comment('7月上限残業時間');
            $table->double('uplimit_time_8month',6,2)->comment('8月上限残業時間');
            $table->double('uplimit_time_9month',6,2)->comment('9月上限残業時間');
            $table->double('uplimit_time_10month',6,2)->comment('10月上限残業時間');
            $table->double('uplimit_time_11month',6,2)->comment('11月上限残業時間');
            $table->double('uplimit_time_12month',6,2)->comment('12月上限残業時間');
            $table->double('max_3month_total',7,2)->comment('３ヶ月累計');
            $table->double('max_6month_total',7,2)->comment('６ヶ月累計');
            $table->double('max_12month_total',7,2)->comment('１年間累計');
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
        Schema::dropIfExists('settings');
    }
}
