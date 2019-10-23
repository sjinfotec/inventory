<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToDemands2141 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('demands', function (Blueprint $table) {
            $table->index(['no', 'log_no'],'no_log_no_index');
        });
        Schema::table('demands', function (Blueprint $table) {
            $table->index(['doc_code', 'no', 'log_no'],'doc_code_no_log_no_index');
        });
        Schema::table('demands', function (Blueprint $table) {
            $table->index(['department_code', 'user_code'],'department_code_user_code_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('demands', function (Blueprint $table) {
            //
        });
    }
}
