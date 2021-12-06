<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToBackOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('back_order', function (Blueprint $table) {
            $table->unique(['order_no', 'seq'],'order_no_seq_index');
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('back_order', function (Blueprint $table) {
            $table->dropUnique('order_no_seq_index');
                //
        });
    }
}
