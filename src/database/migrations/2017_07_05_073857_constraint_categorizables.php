<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ConstraintCategorizables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ry_categories_categorizables', function (Blueprint $table) {
        	$table->foreign("categorie_id")->references("id")->on("ry_categories_categories")->onDelete("cascade");
        	$table->unique(["categorie_id", "categorizable_type", "categorizable_id"], "unique_categorization");
        	$table->unique(["categorizable_type", "categorizable_id", "main"], "categorie_principale");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ry_categories_categorizables', function (Blueprint $table) {
        	$table->dropForeign("ry_categories_categorizables_categorie_id_foreign");
            $table->dropUnique("unique_categorization");
            $table->dropUnique("categorie_principale");
        });
    }
}
