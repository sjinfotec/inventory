<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameColumnProductProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_processes', function (Blueprint $table) {
            $table->renameColumn('product_code', 'processes_code');
            $table->renameColumn('no', 'detail_no');
            //$table->string('processes_code')->comment('工程コード')->change();
            //$table->string('detail_no')->comment('工程行NO')->change();
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
