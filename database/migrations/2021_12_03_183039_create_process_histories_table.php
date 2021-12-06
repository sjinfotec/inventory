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
            $table->unsignedInteger('seq')->comment('連番');
            $table->unsignedInteger('process_history_no')->comment('加工履歴No');
            $table->char('work_kind',1)->comment('作業種別');
            $table->String('row_seq')->nullable()->comment('行');
            $table->unsignedInteger('progress_no')->nullable()->comment('工程NO');
            $table->dateTime('process_history_time')->nullable()->comment('加工時刻');
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
