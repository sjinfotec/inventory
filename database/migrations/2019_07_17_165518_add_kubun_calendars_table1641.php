<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKubunCalendarsTable1641 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('calendars', function (Blueprint $table) {
            $table->unsignedInteger('business_kubun')->after('weekday_kubun')->nullable()->comment('営業日区分');
            $table->unsignedInteger('holiday_kubun')->after('business_kubun')->nullable()->comment('休暇区分');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('calendars', function (Blueprint $table) {
            $table->dropColumn('business_kubun');
            $table->dropColumn('holiday_kubun');
        });
    }
}
