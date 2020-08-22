<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToDownloadLogs08211308 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('download_logs', function (Blueprint $table) {
            //PK設定
            $table->primary(['account_id','downloadfile_no','downloadfile_date','downloadfile_time'],'account_no_date_time_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('download_logs', function (Blueprint $table) {
            $table->dropPrimary();
        });
    }
}
