<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnCompaniesTable1409 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->char('apply_term_to', 8)->after('id')->comment('適用期間終了');
            $table->char('apply_term_from', 8)->after('id')->comment('適用期間開始');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('apply_term_to');
            $table->dropColumn('apply_term_from');
       });
    }
}
