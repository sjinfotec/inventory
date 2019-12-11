<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToDepartments1150 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropIndex('departments_user_code_apply_term_from_index');
        });
        Schema::table('departments', function (Blueprint $table) {
            $table->index(['code'],'code_index');
        });
        Schema::table('departments', function (Blueprint $table) {
            $table->index(['code', 'apply_term_from'],'code_apply_term_from_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropIndex('departments_user_code_apply_term_from_index');
            $table->dropIndex('code_index');
            $table->dropIndex('code_apply_term_from_index');
        });
    }
}
