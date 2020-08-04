<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToCalendarSettingInformations1010 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('calendar_setting_informations', function (Blueprint $table) {
            $table->index(['yearmonth', 'employment_status', 'user_code'],'yearmonth_employment_status_user_code_index');
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
            $table->dropIndex('yearmonth_employment_status_user_code_index');
        });
    }
}
