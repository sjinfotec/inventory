<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnDemandDetails1921 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('demand_details', function (Blueprint $table) {
            $table->char('user_code', 10)->after('row_no')->nullable()->comment('氏名');
            $table->char('department_code', 8)->after('row_no')->nullable()->comment('部署');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('demand_details', function (Blueprint $table) {
            $table->dropColumn('user_code');
            $table->dropColumn('department_code');
        });
    }
}
