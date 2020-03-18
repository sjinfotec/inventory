<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTempCalcWorkingtimes03172056 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('temp_calc_workingtimes', function (Blueprint $table) {
            $table->string('editor_department_name')->nullable()->after('editor_department_code')->comment('編集部署名');
            $table->string('editor_user_name')->nullable()->after('editor_user_code')->comment('編集ユーザー名');
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
            $table->dropColumn('editor_department_name');
            $table->dropColumn('editor_user_name');
        });
    }
}
