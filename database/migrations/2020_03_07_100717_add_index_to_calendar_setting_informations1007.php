<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToCalendarSettingInformations1007 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('calendar_setting_informations', function (Blueprint $table) {
            $table->primary(['yearmonth', 'department_code', 'employment_status', 'user_code'],'yearmonth_department_employment_user_index');
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
