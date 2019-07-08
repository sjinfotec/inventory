<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkingTimetablesTable1124 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('working_timetables', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('no')->comment('タイムテーブルNo');
            $table->String('name')->comment('タイムテーブル名称');
            $table->unsignedInteger('working_time_kubun')->comment('労働時間区分');
            $table->time('from_time')->nullable()->comment('開始時刻');
            $table->time('to_time')->nullable()->comment('終了時刻');
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
        Schema::dropIfExists('working_timetables_table1124');
    }
}
