<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ConstraintCategorylangs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ry_categories_categorylangs', function (Blueprint $table) {
        	$table->integer("categorie_id", false, true)->change();
        	$table->integer("user_id", false, true)->change();
        	$table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
        	$table->foreign("categorie_id")->references("id")->on("ry_categories_categories")->onDelete("cascade");
        	$table->unique(['lang', 'categorie_id'], "categorie_trans");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ry_categories_categorylangs', function (Blueprint $table) {
        	$table->dropUnique("categorie_trans");
        	$table->dropForeign("ry_categories_categorylangs_user_id_foreign");
        	$table->dropIndex("ry_categories_categorylangs_user_id_foreign");
        	$table->dropForeign("ry_categories_categorylangs_categorie_id_foreign");
        	$table->dropIndex("ry_categories_categorylangs_categorie_id_foreign");
        });
    }
}
