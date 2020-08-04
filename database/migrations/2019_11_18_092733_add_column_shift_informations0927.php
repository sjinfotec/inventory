<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnShiftInformations0927 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shift_informations', function (Blueprint $table) {
            $table->char('target_date', 8)->after('department_code')->nullable()->comment('シフト勤務対象日付');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shift_informations', function (Blueprint $table) {
            $table->dropColumn('target_date');
        });
    }
}
