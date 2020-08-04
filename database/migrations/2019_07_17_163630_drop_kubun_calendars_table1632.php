<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropKubunCalendarsTable1632 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('calendars', function (Blueprint $table) {
            $table->dropColumn('leave_kubun');
            $table->dropColumn('holiday_kubun');
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
            $table->unsignedInteger('leave_kubun')->comment('休暇区分');
            $table->unsignedInteger('holiday_kubun')->comment('休日区分');
        });
    
    }
}
