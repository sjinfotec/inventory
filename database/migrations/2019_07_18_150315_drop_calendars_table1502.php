<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropCalendarsTable1502 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::dropIfExists('calendars');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('calendars', function (Blueprint $table) {
            $table->date('date')->comment('日付');
            $table->unsignedInteger('weekday_kubun')->comment('曜日区分');
            $table->unsignedInteger('business_kubun')->nullable()->comment('営業日区分');
            $table->unsignedInteger('holiday_kubun')->nullable()->comment('休暇区分');
            $table->String('created_user')->nullable()->comment('作成ユーザー');
            $table->String('updated_user')->nullable()->comment('修正ユーザー');
            $table->timestamps();
            $table->boolean('is_deleted')->default(0)->comment('削除フラグ');
        });
    }
}
