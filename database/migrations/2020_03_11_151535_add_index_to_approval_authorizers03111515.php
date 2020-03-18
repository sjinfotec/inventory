<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToApprovalAuthorizers03111515 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('approval_authorizers', function (Blueprint $table) {
            $table->index(['approval_route_no', 'seq', 'main_sub', 'approval_department_code', 'approval_user_code'],'route_no_seq_main_sub_department_user_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('approval_authorizers', function (Blueprint $table) {
            $table->dropIndex('route_no_seq_main_sub_department_user_index');
        });
    }
}
