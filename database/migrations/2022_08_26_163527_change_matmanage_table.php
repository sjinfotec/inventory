<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeMatmanageTable extends Migration
{
    /**
     * Run the migrations.
     *            //$table->double('unit_price', 10, 2)->change();
     *            //$table->double('total', 12, 2)->change();
     * @return void
     */
    public function up()
    {
        Schema::table('matmanage', function (Blueprint $table) {
            $table->decimal('unit_price', $precision = 10, $scale = 2)->change();
            $table->decimal('total', $precision = 12, $scale = 2)->change();

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
