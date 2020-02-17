<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnDemand1803 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('approvals', function (Blueprint $table) {
            $table->char('no',14)->after('id')->comment('申請番号');
        });
        Schema::table('demand_details', function (Blueprint $table) {
            $table->char('no',14)->after('id')->comment('申請番号');
        });
        Schema::table('demands', function (Blueprint $table) {
            $table->char('no',14)->after('id')->comment('申請番号');
            $table->char('demand_now',8)->after('doc_code')->comment('申請当日日付');
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
            $table->dropColumn('no');
        });
        Schema::table('demand_details', function (Blueprint $table) {
            $table->dropColumn('no');
        });
        Schema::table('demands', function (Blueprint $table) {
            $table->dropColumn('no');
            $table->dropColumn('demand_now');
        });
    }
}
