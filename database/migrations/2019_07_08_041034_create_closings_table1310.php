<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClosingsTable1310 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('closings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('closings_1month')->comment('1月締');
            $table->unsignedInteger('closings_2month')->comment('2月締');
            $table->unsignedInteger('closings_3month')->comment('3月締');
            $table->unsignedInteger('closings_4month')->comment('4月締');
            $table->unsignedInteger('closings_5month')->comment('5月締');
            $table->unsignedInteger('closings_6month')->comment('6月締');
            $table->unsignedInteger('closings_7month')->comment('7月締');
            $table->unsignedInteger('closings_8month')->comment('8月締');
            $table->unsignedInteger('closings_9month')->comment('9月締');
            $table->unsignedInteger('closings_10month')->comment('10月締');
            $table->unsignedInteger('closings_11month')->comment('11月締');
            $table->unsignedInteger('closings_12month')->comment('12月締');
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
        Schema::dropIfExists('closings');
    }
}
