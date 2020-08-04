<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalendarSettingInformations0928 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendar_setting_informations', function (Blueprint $table) {
            $table->char('yearmonth',6)->comment('年月');
            $table->char('department_code',8)->comment('部署コード');
            $table->unsignedInteger('employment_status')->comment('雇用形態');
            $table->char('user_code',10)->comment('ユーザーコード');
            $table->unsignedInteger('setting_ptn')->comment('設定パターン');
            $table->unsignedInteger('weekday_kubun')->nullable()->comment('曜日区分');
            $table->unsignedInteger('business_kubun')->nullable()->comment('営業日区分');
            $table->unsignedInteger('holiday_kubun')->nullable()->comment('休暇区分');
            $table->String('created_user')->nullable()->comment('作成ユーザー');
            $table->String('updated_user')->nullable()->comment('修正ユーザー');
            $table->boolean('is_deleted')->default(0)->comment('削除フラグ');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calendar_setting_informations');
    }
}
