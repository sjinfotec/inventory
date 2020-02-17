<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTempWorkingTimeDates1146 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('temp_working_time_dates', function (Blueprint $table) {
            $table->datetime('public_going_out_return_time_5')->after('missing_middle_return_time_5')->nullable()->comment('公用外出戻り時刻５');
            $table->datetime('public_going_out_return_time_4')->after('missing_middle_return_time_5')->nullable()->comment('公用外出戻り時刻４');
            $table->datetime('public_going_out_return_time_3')->after('missing_middle_return_time_5')->nullable()->comment('公用外出戻り時刻３');
            $table->datetime('public_going_out_return_time_2')->after('missing_middle_return_time_5')->nullable()->comment('公用外出戻り時刻２');
            $table->datetime('public_going_out_return_time_1')->after('missing_middle_return_time_5')->nullable()->comment('公用外出戻り時刻１');
            $table->datetime('public_going_out_time_5')->after('missing_middle_return_time_5')->nullable()->comment('公用外出時刻５');
            $table->datetime('public_going_out_time_4')->after('missing_middle_return_time_5')->nullable()->comment('公用外出時刻４');
            $table->datetime('public_going_out_time_3')->after('missing_middle_return_time_5')->nullable()->comment('公用外出時刻３');
            $table->datetime('public_going_out_time_2')->after('missing_middle_return_time_5')->nullable()->comment('公用外出時刻２');
            $table->datetime('public_going_out_time_1')->after('missing_middle_return_time_5')->nullable()->comment('公用外出時刻１');
            $table->double('missing_middle_hours',6,2)->after('off_hours_working_hours')->nullable()->comment('私用外出時間');
            $table->double('public_going_out_hours',6,2)->after('off_hours_working_hours')->nullable()->comment('公用外出時間');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('temp_working_time_dates', function (Blueprint $table) {
            $table->dropColumn('public_going_out_return_time_5');
            $table->dropColumn('public_going_out_return_time_4');
            $table->dropColumn('public_going_out_return_time_3');
            $table->dropColumn('public_going_out_return_time_2');
            $table->dropColumn('public_going_out_return_time_1');
            $table->dropColumn('public_going_out_time_5');
            $table->dropColumn('public_going_out_time_4');
            $table->dropColumn('public_going_out_time_3');
            $table->dropColumn('public_going_out_time_2');
            $table->dropColumn('public_going_out_time_1');
            $table->dropColumn('missing_middle_hours');
            $table->dropColumn('public_going_out_hours');
        });
    }
}
