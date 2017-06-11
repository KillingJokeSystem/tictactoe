<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Auth;
use App\User as User;
use App\Games as Games;
use App\Players as Players;
use App\Moves as Moves;

class CreationController extends Controller
{
    //
    public function create_game($size, $win_condition, $turn_timeout){
	$idAccount = Auth::id();
        $id = Games::insertGetId([
		"start_date" => date("Y-m-d H:i:s"),
		"grid_size" => $size, 
		"win_condition" => $win_condition, 
		"turn_timeout" => $turn_timeout, 
		"ended" => 0 
	]);
	
	$is_first = rand(0,1);	

        Players::insert([
		"games_id" => $id,
		"users_id" => $idAccount, 
		"first" => $is_first, 
		"winner" => 0 
	]);

        Players::insert([
		"games_id" => $id,
		"users_id" => 1, 
		"first" => 1-$is_first, 
		"winner" => 0 
	]);
    }
}
