<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTempCalcWorkingtimes03171648 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('temp_calc_workingtimes', function (Blueprint $table) {
            $table->char('editor_user_code',10)->nullable()->after('work_times_id')->comment('編集ユーザーコード');
            $table->char('editor_department_code',8)->nullable()->after('work_times_id')->comment('編集部署コード');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('temp_calc_workingtimes', function (Blueprint $table) {
            $table->dropColumn('editor_user_code');
            $table->dropColumn('editor_department_code');
        });
    }
}
