<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToCalendarSettingInformations0948 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('calendar_setting_informations', function (Blueprint $table) {
            $table->primary(['yearmonth', 'department_code', 'user_code'],'yearmonth_department_code_user_code_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('calendar_setting_informations', function (Blueprint $table) {
            $table->dropPrimary();
        });
    }
}
