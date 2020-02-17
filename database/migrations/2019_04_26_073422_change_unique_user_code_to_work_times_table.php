<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUniqueUserCodeToWorkTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_times', function (Blueprint $table) {
            $table->dropUnique('work_times_user_code_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_times', function (Blueprint $table) {
             $table->unique('user_code','work_times_user_code_unique');
        }); 
    }
}
