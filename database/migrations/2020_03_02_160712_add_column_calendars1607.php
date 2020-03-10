<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnCalendars1607 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('calendars', function (Blueprint $table) {
            $table->char('user_code', 10)->nullable()->after('date')->comment('ユーザーコード');
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
            $table->dropColumn('user_code');
        });
    }
}
