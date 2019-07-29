<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnWorkingTimeDatesTable1455 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('working_time_dates', function (Blueprint $table) {
            //カラム追加
            $table->datetime('missing_middle_return_time_5')->after('missing_middle_time_5')->nullable()->comment('戻り時刻５');
            $table->datetime('missing_middle_return_time_4')->after('missing_middle_time_5')->nullable()->comment('戻り時刻４');
            $table->datetime('missing_middle_return_time_3')->after('missing_middle_time_5')->nullable()->comment('戻り時刻３');
            $table->datetime('missing_middle_return_time_2')->after('missing_middle_time_5')->nullable()->comment('戻り時刻２');
            $table->datetime('missing_middle_return_time_1')->after('missing_middle_time_5')->nullable()->comment('戻り時刻１');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
