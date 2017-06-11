<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMoveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('moves', function( Blueprint $table){
            $table->integer('players_id');
            $table->integer('games_id');
            $table->integer('turn');
            $table->integer('x');
            $table->integer('y');
	    $table->dateTime("played_at");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('moves');
    }
}
