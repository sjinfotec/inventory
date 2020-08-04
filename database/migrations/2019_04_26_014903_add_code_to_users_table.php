<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCodeToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->char('department_code', 8)->unique()->after('id')->comment('部署コード');
            $table->string('login_id',30)->unique()->after('email')->comment('ログインID');
            $table->unsignedInteger('role')->default(0)->comment('権限');
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
            $table->dropColumn('department_code');
            $table->dropColumn('login_id');
            $table->dropColumn('role');
        });
    }
}
