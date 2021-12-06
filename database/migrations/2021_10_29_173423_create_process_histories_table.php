<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcessHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('process_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('order_no',12)->comment('受注番号');
            $table->char('seq',5)->comment('行');
            $table->unsignedInteger('progress_no')->comment('工程NO');
            $table->char('process_history_no',4)->nullable()->comment('加工履歴No');
            $table->dateTime('process_history_start')->nullable()->comment('加工開始時刻');
            $table->dateTime('process_history_end')->nullable()->comment('加工終了時刻');
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
        Schema::dropIfExists('process_histories');
    }
}
