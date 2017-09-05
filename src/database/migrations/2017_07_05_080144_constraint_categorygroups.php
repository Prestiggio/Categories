<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ConstraintCategorygroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ry_categories_categorygroups', function (Blueprint $table) {
        	$table->unique("name");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ry_categories_categorygroups', function (Blueprint $table) {
        	$table->dropUnique("ry_categories_categorygroups_name_unique");
        });
    }
}
