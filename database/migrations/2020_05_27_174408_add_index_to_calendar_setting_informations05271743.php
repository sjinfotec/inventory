<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToCalendarSettingInformations05271743 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('calendar_setting_informations', function (Blueprint $table) {
            $table->index(['date', 'department_code', 'user_code'],'date_department_user_index');
            $table->index(['user_code', 'date', 'department_code'],'user_date_department_index');
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
            $table->dropIndex('date_department_user_index');
            $table->dropIndex('user_date_department_index');
        });
    }
}
