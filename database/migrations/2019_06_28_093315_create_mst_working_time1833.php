<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstWorkingTime1833 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_working_time', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->String('working_time_style')->comment('労働時間形態');
            $table->String('code')->comment('コード');
            $table->unsignedInteger('sort_seq')->comment('並び順');
            $table->String('working_time_kubun')->nullable()->comment('労働時間区分');
            $table->String('time_from')->nullable()->comment('開始時刻');
            $table->String('time_to')->nullable()->comment('終了時刻');
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
        Schema::dropIfExists('mst_working_time');
    }
}
