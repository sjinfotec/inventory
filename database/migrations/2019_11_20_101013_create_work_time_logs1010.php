<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkTimeLogs1010 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_time_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('user_code',10)->comment('ユーザーコード');
            $table->char('department_code',8)->comment('部署コード');
            $table->unsignedInteger('employment_status')->comment('雇用形態');
            $table->dateTime('record_time')->comment('打刻時間');
            $table->unsignedInteger('mode')->comment('打刻モード');
            $table->String('card_idm')->nullable()->comment('カードＩＤ');
            $table->String('created_user')->nullable()->comment('作成ユーザー');
            $table->String('updated_user')->nullable()->comment('修正ユーザー');
            $table->boolean('is_deleted')->default(0)->nullable()->comment('削除フラグ');
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
        Schema::dropIfExists('work_time_logs');
    }
}
