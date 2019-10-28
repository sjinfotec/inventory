<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnDemands1926 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('approvals', function (Blueprint $table) {
            $table->char('nmail_user_code',10)->after('mail_address')->comment('メール宛先者');
            $table->char('nmail_department_code',8)->after('mail_address')->comment('メール宛先者部署');
        });
        Schema::table('demands', function (Blueprint $table) {
            $table->char('nmail_user_code',10)->after('mail_address')->comment('メール宛先者');
            $table->char('nmail_department_code',8)->after('mail_address')->comment('メール宛先者部署');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('approvals', function (Blueprint $table) {
            $table->dropColumn('nmail_user_code');
            $table->dropColumn('nmail_department_code');
        });
        Schema::table('demands', function (Blueprint $table) {
            $table->dropColumn('nmail_user_code');
            $table->dropColumn('nmail_department_code');
        });
    }
}
