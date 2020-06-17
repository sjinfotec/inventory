<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnFeatureItemSelections06170859 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('feature_item_selections', function (Blueprint $table) {
            $table->String('description')->nullable()->after('value_select')->comment('説明');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('feature_item_selections', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
}
