<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMarksInventoryATable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_a', function (Blueprint $table) {
            $table->String('marks',10)->nullable()->after('other1')->comment('マーク');
            $table->boolean('is_deleted')->default(0)->after('updated_at')->comment('削除フラグ');
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory_a', function (Blueprint $table) {
            $table->dropColumn('marks');
            $table->dropColumn('is_deleted');
            //
        });
    }
}
