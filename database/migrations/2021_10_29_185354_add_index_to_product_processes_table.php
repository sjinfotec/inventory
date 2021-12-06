<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToProductProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_processes', function (Blueprint $table) {
            //
            $table->unique(['product_code', 'no'],'product_code_code_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_processes', function (Blueprint $table) {
            $table->dropIndex('product_code_code_index');
            //
        });
    }
}
