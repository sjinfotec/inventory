<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSettings2243 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->double('max_2month_total', 7, 2)->after('time_rounding')->nullable()->comment('２ヶ月累計');
            $table->double('max_1month_total', 7, 2)->after('time_rounding')->nullable()->comment('１ヶ月累計');
            $table->double('max_1month_total_sp', 7, 2)->after('max_12month_total')->nullable()->comment('１ヶ月累計（特別条項）');
            $table->double('max_12month_total_sp', 7, 2)->after('max_12month_total')->nullable()->comment('１２ヶ月累計（特別条項）');
            $table->double('ave_2_6_time_sp', 7, 2)->after('max_12month_total')->nullable()->comment('２－６ヶ月平均（特別条項）');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('max_2month_total');
            $table->dropColumn('max_1month_total');
            $table->dropColumn('max_1month_total_sp');
            $table->dropColumn('max_12month_total_sp');
            $table->dropColumn('ave_2_6_time_sp');
        });
    }
}
