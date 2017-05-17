<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RyCategoriesCategoriesHasmain extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ry_categories_categorizables', function (Blueprint $table) {
            $table->boolean("main")->nullable()->after("categorizable_id");
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
            //
        });
    }
}
