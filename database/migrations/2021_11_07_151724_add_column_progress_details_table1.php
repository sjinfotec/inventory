<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnProgressDetailsTable1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('progress_details', function (Blueprint $table) {
            $table->unsignedInteger('product_processes_detail_no')->after('id')->comment('工程行NO');
            $table->char('product_processes_code',2)->after('id')->comment('工程コード');
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
