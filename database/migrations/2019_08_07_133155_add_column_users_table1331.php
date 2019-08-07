<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnUsersTable1331 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->char('apply_term_to', 8)->after('id')->comment('適用期間終了');
            $table->char('apply_term_from', 8)->after('id')->comment('適用期間開始');
            $table->String('updated_user')->after('updated_at')->nullable()->comment('修正ユーザー');
            $table->String('created_user')->after('updated_at')->nullable()->comment('作成ユーザー');
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
            $table->dropColumn('apply_term_to');
            $table->dropColumn('apply_term_from');
            $table->dropColumn('updated_user');
            $table->dropColumn('created_user');
       });
   }
}
