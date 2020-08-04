<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceLogs0920 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('department_code',8)->comment('部署コード');
            $table->unsignedInteger('employment_status')->comment('雇用形態');
            $table->char('user_code',10)->comment('ユーザー');
            $table->char('working_date',8)->comment('日付');
            $table->unsignedInteger('event_mode')->nullable()->comment('ＰＣイベントモード');
            $table->datetime('event_time')->nullable()->comment('ＰＣイベント時間');
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
        Schema::dropIfExists('attendance_logs');
    }
}
