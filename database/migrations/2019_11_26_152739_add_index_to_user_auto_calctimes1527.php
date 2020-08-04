<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToUserAutoCalctimes1527 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_auto_calctimes', function (Blueprint $table) {
            $table->index(['target_ym', 'department_code', 'user_code'],'target_ym_department_code_user_code_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_auto_calctimes', function (Blueprint $table) {
            $table->dropIndex('target_ym_department_code_user_code_index');
        });
    }
}
