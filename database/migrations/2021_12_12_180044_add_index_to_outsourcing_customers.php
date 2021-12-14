<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToOutsourcingCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('outsourcing_customers', function (Blueprint $table) {
            $table->unique(['code'],'code_index');
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
        Schema::table('outsourcing_customers', function (Blueprint $table) {
            $table->dropUnique('code_index');
            //
        });
    }
}
