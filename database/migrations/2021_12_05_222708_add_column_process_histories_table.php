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
            $table->char('user_code',10)->nullable()->after('work_kind')->comment('作業者コード');
            $table->char('device_code',6)->nullable()->after('work_kind')->comment('機器コード');
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
