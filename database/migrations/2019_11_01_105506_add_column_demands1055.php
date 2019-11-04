<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnDemands1055 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('demands', function (Blueprint $table) {
            $table->dropColumn('approval_seq');
        });
        Schema::table('demands', function (Blueprint $table) {
            $table->unsignedInteger('nmail_seq')->after('nmail_user_code')->comment('承認者シーケンス');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('demands', function (Blueprint $table) {
            $table->dropColumn('nmail_seq');
        });
    }
}
