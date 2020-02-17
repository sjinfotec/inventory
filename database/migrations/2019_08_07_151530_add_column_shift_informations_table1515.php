<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnShiftInformationsTable1515 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shift_informations', function (Blueprint $table) {
            $table->bigInteger('department_id')->after('user_code')->nullable()->comment('部署ID');
        });
     }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shift_informations', function (Blueprint $table) {
            $table->dropColumn('department_id');
       });
    }
}
