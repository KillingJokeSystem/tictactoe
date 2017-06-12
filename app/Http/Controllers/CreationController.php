<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Auth;
use App\User as User;
use App\Games as Games;
use App\Players as Players;
use App\Moves as Moves;
use App\Matchmakings as Matchmakings;

class CreationController extends Controller
{
    //
    public function create_matchmaking($size, $win_condition, $turn_timeout){

	$id = Matchmakings::insertGetId([
                "heart_beat" => date("Y-m-d H:i:s"),
                "creation_date" => date("Y-m-d H:i:s"),
                "grid_size" => $size,
                "win_condition" => $win_condition,
                "turn_timeout" => $turn_timeout,
                "games_id" => -1,
		"matched" => false
        ]);
	echo json_encode(array("response" => 1, "mm_id" => $id));
    }

    public function get_heart_beat($mm_id){
	$mm  = Matchmakings::find($mm_id);
	echo json_encode(array("response" => 1, "data" => $mm));
    }

    public function update_heart_beat($mm_id){
	$res = Matchmakings::where('id', '=', $mm_id)->update(["heart_beat" => date("Y-m-d H:i:s")]);
	$mm  = Matchmakings::find($mm_id);
	echo json_encode(array("response" => 1, "data" => $mm));
    }

    public function get_matchmaking($size, $win_condition, $turn_timeout){
	$mm  = Matchmakings::where('heart_beat', '>=', date("Y-m-d H:i:s", time() - 2))
			->where('matched', '=', false)
			->where('grid_size', '=', $size)
			->where('win_condition', '=', $win_condition)
			->where('turn_timeout', '=', $turn_timeout)
			->orderBy('creation_date', 'asc')->first();
	if( isset($mm->id) ){
	    $mm_id = $mm->id;
	    $res = Matchmakings::where('id', '=', $mm_id)->update(["matched" => true]);
	    $mm = Matchmakings::find($mm_id);
	    echo json_encode(array("response" => 1, "data" => $mm));
	}
	else{
	    echo json_encode(array("response" => 0));
	}
    }

    public function create_game($mm_id){
	$idAccount = Auth::id();
	$mm = Matchmakings::find($mm_id);
        $id = Games::insertGetId([
		"start_date" => date("Y-m-d H:i:s"),
		"grid_size" => $mm->grid_size, 
		"win_condition" => $mm->win_condition, 
		"turn_timeout" => $mm->turn_timeout, 
		"ended" => 0 
	]);

	$res = Matchmakings::where('id', '=', $mm_id)->update(["games_id" => $id]);
	
	$is_first = rand(0,1);	

        Players::insert([
		"games_id" => $id,
		"users_id" => $idAccount, 
		"first" => $is_first, 
		"winner" => 0 
	]);
    }

    public function join_game($mm_id){
	$idAccount = Auth::id();
        $mm = Matchmakings::find($mm_id);
	$id = $mm->games_id;
	$player = Players::where('games_id', '=', $id)->first();
	$is_first = 1 - $player->first;

        Players::insert([
		"games_id" => $id,
		"users_id" => $idAccount, 
		"first" => $is_first, 
		"winner" => 0 
	]);
    }
}
