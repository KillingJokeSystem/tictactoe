<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMatchmakingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
	Schema::create('matchmakings', function( Blueprint $table){
            $table->increments('id');
            $table->dateTime("heart_beat");
            $table->dateTime("creation_date");
            $table->integer('grid_size');
            $table->integer('win_condition');
            $table->integer('turn_timeout');
            $table->integer('games_id');
            $table->boolean('matched');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
	Schema::dropIfExists('matchmakings');
    }
}
