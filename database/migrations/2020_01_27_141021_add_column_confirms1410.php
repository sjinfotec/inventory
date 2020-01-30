<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnConfirms1410 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('confirms', function (Blueprint $table) {
            $table->char('confirm_department_code',8)->nullable()->after('main_sub')->comment('ルート適用部署');
            $table->char('user_department_code',8)->after('confirm_department_code')->comment('ユーザー部署コード');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('confirms', function (Blueprint $table) {
            $table->dropColumn('confirm_department_id');
            $table->dropColumn('user_department_id');
        });
    }
}
