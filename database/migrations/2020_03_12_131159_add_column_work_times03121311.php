<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnWorkTimes03121311 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_times', function (Blueprint $table) {
            $table->char('editor_user_code',1)->nullable()->after('check_interval')->comment('編集フラグ　true:勤怠編集');
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
            $table->dropColumn('editor_user_code');
        });
    }
}
