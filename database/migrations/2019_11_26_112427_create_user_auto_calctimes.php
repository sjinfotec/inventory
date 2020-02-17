<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAutoCalctimes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_auto_calctimes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('target_date',8)->comment('対象日付');
            $table->char('department_code',8)->comment('部署コード');
            $table->char('user_code',10)->comment('ユーザーコード');
            $table->dateTime('start_time')->comment('開始時刻');
            $table->dateTime('end_time')->comment('終了時刻');
            $table->char('status', 2)->comment('実行結果');
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
        Schema::dropIfExists('user_auto_calctimes');
    }
}
