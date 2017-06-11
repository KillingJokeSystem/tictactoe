<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function( Blueprint $table){
	    $table->increments('id');
	    $table->dateTime("start_date");
	    $table->integer('grid_size');
	    $table->integer('win_condition');
	    $table->integer('turn_timeout');
	    $table->boolean('ended');
	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games');
    }
}
