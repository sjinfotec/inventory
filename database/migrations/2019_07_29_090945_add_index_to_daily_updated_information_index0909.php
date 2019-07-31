<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToDailyUpdatedInformationIndex0909 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daily_updated_informations', function (Blueprint $table) {
            //
            $table->index(['department_id','user_code'],'daily_updated_informations_department_id_user_code_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('daily_updated_informations', function (Blueprint $table) {
            //
        });
    }
}
