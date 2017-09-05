<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ConstraintCategoriesgroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ry_categories_categories', function (Blueprint $table) {
            $table->foreign("categorygroup_id")->references("id")->on("ry_categories_categorygroups")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ry_categories_categories', function (Blueprint $table) {
            $table->dropForeign("ry_categories_categories_categorygroup_id_foreign");
            $table->dropIndex("ry_categories_categories_categorygroup_id_foreign");
        });
    }
}
