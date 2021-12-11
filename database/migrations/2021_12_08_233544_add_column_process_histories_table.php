<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnProcessHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('process_histories', function (Blueprint $table) {
            $table->unsignedInteger('process_time_m')->nullable()->after('process_history_time')->comment('作業時間M');
            $table->unsignedInteger('process_time_h')->nullable()->after('process_history_time')->comment('作業時間H');
        });
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
