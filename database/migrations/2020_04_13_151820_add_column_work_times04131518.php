<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnWorkTimes04131518 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_times', function (Blueprint $table) {
            $table->bigInteger('user_holiday_kubuns_id')->nullable()->after('mode')->comment('ユーザー休暇区分ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_times', function (Blueprint $table) {
            $table->dropColumn('user_holiday_kubuns_id');
        });
    }
}
