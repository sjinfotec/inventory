<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstSmTable1844 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_sm', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('sm_1month')->comment('1月締');
            $table->unsignedInteger('sm_2month')->comment('2月締');
            $table->unsignedInteger('sm_3month')->comment('3月締');
            $table->unsignedInteger('sm_4month')->comment('4月締');
            $table->unsignedInteger('sm_5month')->comment('5月締');
            $table->unsignedInteger('sm_6month')->comment('6月締');
            $table->unsignedInteger('sm_7month')->comment('7月締');
            $table->unsignedInteger('sm_8month')->comment('8月締');
            $table->unsignedInteger('sm_9month')->comment('9月締');
            $table->unsignedInteger('sm_10month')->comment('10月締');
            $table->unsignedInteger('sm_11month')->comment('11月締');
            $table->unsignedInteger('sm_12month')->comment('12月締');
            $table->unsignedInteger('beginning_month')->comment('期首月');
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
        Schema::dropIfExists('mst_sm');
    }
}
