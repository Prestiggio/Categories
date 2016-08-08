<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Categorylangs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categorylangs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("categorie_id");
            $table->integer("user_id");
            $table->char("lang");
            $table->char("name");
            $table->text("descriptif");
            $table->text("path")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('categorylangs');
    }
}
