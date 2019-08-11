<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnAllTable1708 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 各テーブルのapply_term_to削除
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('apply_term_to');
        });
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('apply_term_to');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('apply_term_to');
        });
        Schema::table('working_timetables', function (Blueprint $table) {
            $table->dropColumn('apply_term_to');
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
