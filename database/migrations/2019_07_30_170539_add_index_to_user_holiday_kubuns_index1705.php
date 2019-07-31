<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToUserHolidayKubunsIndex1705 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_holiday_kubuns', function (Blueprint $table) {
            $table->index(['working_date','department_id','user_code'],'user_holiday_kubuns_date_department_user_code_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_holiday_kubuns', function (Blueprint $table) {
            //
        });
    }
}
