<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnImportBackOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('import_back_order', function (Blueprint $table) {
            $table->unsignedInteger('out_seq')->after('id')->comment('出力順');
            $table->unsignedInteger('unit_price')->after('outline_name')->comment('単価');
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
