<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToConfirms1312 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('confirms', function (Blueprint $table) {
            //
            $table->index(['confirm_no', 'seq','main_sub'],'confirm_no_seq_main_sub_index');
            $table->index(['user_department_id','user_code'],'user_department_id_user_code_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('confirm_no_seq_main_sub_index');
            $table->dropIndex('user_department_id_user_code_index');
        });
    }
}
