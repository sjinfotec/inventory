<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToApprovalRouteNos03111456 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('approval_route_nos', function (Blueprint $table) {
            $table->index(['approval_route_no', 'apply_department_code'],'approval_route_no_apply_department_code_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('approval_route_nos', function (Blueprint $table) {
            $table->dropIndex('approval_route_no_apply_department_code_index');
        });
    }
}
