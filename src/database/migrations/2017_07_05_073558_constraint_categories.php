<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ConstraintCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ry_categories_categories', function (Blueprint $table) {
        	$table->integer("parent_id", false, true)->change();
        	$table->foreign("parent_id", "ry_categories_categories_parent_id_index")->references("id")->on("ry_categories_categories")->onDelete("cascade");
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
        	$table->dropForeign("ry_categories_categories_parent_id_index");
        });
    }
}
