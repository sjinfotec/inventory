<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnProgressDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('progress_details', function (Blueprint $table) {
            $table->unsignedInteger('process_time_h')->after('process_history_no')->comment('加工連番合計（時）');
            $table->unsignedInteger('process_time_m')->after('process_history_no')->comment('加工連番合計（分）');
            $table->unsignedInteger('setup_time_h')->after('setup_history_no')->comment('段取り連番合計（時）');
            $table->unsignedInteger('setup_time_m')->after('setup_history_no')->comment('段取り連番合計（分）');
        });
        //
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
