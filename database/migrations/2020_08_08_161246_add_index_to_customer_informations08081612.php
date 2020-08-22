<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToCustomerInformations08081612 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_informations', function (Blueprint $table) {
            $table->unique(['account_id', 'entry_type', 'entry_date', 'entry_time'],'account_type_date_time_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_informations', function (Blueprint $table) {
            $table->dropIndex('account_type_date_time_index');
        });
    }
}
