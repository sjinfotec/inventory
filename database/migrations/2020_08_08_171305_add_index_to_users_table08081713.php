<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToUsersTable08081713 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
//            $table->integer('id')->change();
//            $table->dropPrimary();
            $table->String('created_at')->comment('�쐬���[�U�[')->change();
            $table->primary(['account_id', 'apply_term_from', 'code', 'is_deleted', 'created_at'], 'account_apply_code_deleted_created');
            $table->dropColumn('id');
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
            //
        });
    }
}
