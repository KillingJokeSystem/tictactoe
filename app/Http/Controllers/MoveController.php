<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Auth;
use App\User as User;
use App\Games as Games;
use App\Players as Players;
use App\Moves as Moves;

class MoveController extends Controller
{
    public function check_status(){
	$id = Auth::id();
        $players = Players::where('users_id', '=', $id)->orderBy('games_id', 'desc')->first();
        $games = Games::find($players->games_id);

	$last_move = Moves::where('games_id', '=', $games->id)->orderBy('turn', 'desc')->first();
	if( isset($last_move->players_id) ){
	    $is_player_turn = ((1-$players->first)%2 != $last_move->turn%2) ? 1 : 0;
	    $last_move['is_player_turn'] = $is_player_turn;

	    $moves = Moves::where('games_id', '=', $games->id)->orderBy('turn', 'asc')->get();
	    $ended = $this->check_winning_play( $moves, $games->grid_size, $games->win_condition );
	    if( $ended == 1 & $is_player_turn == 1 | sizeof($moves) >= pow($games->grid_size,2) ){
	        $res = Games::where('id', '=', $players->games_id)->update(["ended" => 1]);
		if( sizeof($moves) >= pow($games->grid_size,2) ) $ended = -1;
	    }
	    else if( $ended == 1 & $is_player_turn == 0 ){
	        $res = Players::where(['games_id' => $players->games_id, 
	  				'users_id' => $id])->update(["winner" => 1]);
	    }

	    $last_move["winning_play"] = $ended;

	    echo json_encode(array("response" => 1, "data" => $last_move));
	}
        else {
            echo json_encode(array("response" => 0));
        }
    }

    //set the player move
    public function send_position($pos){
	$id = Auth::id();
        $players = Players::where('users_id', '=', $id)->orderBy('games_id', 'desc')->first();
        $games = Games::find($players->games_id);
	
	if($games->ended == 0){
	    $move = Moves::where('games_id', '=', $games->id)->orderBy('turn', 'desc')->first();
	    $turn = 1;
	    if( isset($move->turn) ){
		$turn = $move->turn+1;
	    }
	    $split_pos = explode(':', $pos);
            Moves::insert([
		"players_id" => $players->id, 
		"games_id" => $players->games_id, 
		"turn" => $turn, 
		"x" => $split_pos[0], 
		"y" => $split_pos[1], 
		"played_at" => date("Y-m-d H:i:s")
	    ]);


	    $move = Moves::where('games_id', '=', $games->id)->orderBy('turn', 'desc')->first();
            echo json_encode(array("response" => 1, "data" => $move));
        }
        else {
            echo json_encode(array("response" => 0));
        }
    }

    public function get_game(){
	$id = Auth::id();
	$players = Players::where('users_id', '=', $id)->orderBy('games_id', 'desc')->first();
	if( isset($players->games_id) ){
	    $games = Games::find($players->games_id);
	
	    if($games->ended == 0){
	        $moves = Moves::where('games_id', '=', $games->id)->orderBy('turn', 'asc')->get();
	        echo json_encode(
		    array("response" => 1, 
			"data" => array(
				"game" => $games,
				"player" => $players,
				"moves" => $moves
			)
		    )
	        );
	    }
	    else {
	        echo json_encode(array("response" => 0));
	    }
	}
	else {
            echo json_encode(array("response" => 0));
        }
    }

    private function check_winning_play( $moves, $size, $win_condition ){
	$table = array();
	for ($x = 1; $x <= $size; $x++) {
	    $table[$x] = array();
	    for ($y = 1; $y <= $size; $y++) {
		$table[$x][$y] = 0;
	    }
	}
	foreach( $moves as $move ){
	    $table[$move["x"]][$move["y"]] = $move["players_id"];
	}

	for ($x = 1; $x <= $size; $x++) {
	    $count = 0;
	    $last_id = 0;
	    for ($y = 1; $y <= $size; $y++) {
		if( $last_id != 0 & $last_id == $table[$x][$y] ) {
		    $count += 1;
		}
		else {
		    $count = 1;
		}
		$last_id = $table[$x][$y];
		if( $count == $win_condition ) return 1;
	    }
	}

	for ($y = 1; $y <= $size; $y++) {
	    $count = 0;
	    $last_id = 0;
	    for ($x = 1; $x <= $size; $x++) {
		if( $last_id != 0 & $last_id == $table[$x][$y] ) $count += 1;
                else $count = 1;
                $last_id = $table[$x][$y];
                if( $count == $win_condition ) return 1;
	    }
	}

	for ($b = 1; $b <= $size; $b++) {
	    for ($c = 1; $c <= $size; $c++) {
		$y = $b;
		$x = $c;
		$count = 0;
        	$last_id = 0;
		while( isset($table[$x][$y]) ) {
		    if( $last_id != 0 & $last_id == $table[$x][$y] ) $count += 1;
        	    else $count = 1;
        	    $last_id = $table[$x][$y];
        	    if( $count == $win_condition ) return 1;
		    $y++;
		    $x++;
		}
	    }
	}

	for ($b = $size; $b >= 1; $b--) {
            for ($c = 1; $c <= $size; $c++) {
                $y = $b;
                $x = $c;
                $count = 0;
                $last_id = 0;
                while( isset($table[$x][$y]) ) {
                    if( $last_id != 0 & $last_id == $table[$x][$y] ) $count += 1;
                    else $count = 1;
                    $last_id = $table[$x][$y];
                    if( $count == $win_condition ) return 1;
                    $y--;
                    $x++;
                }
            }
        }

	return 0;
    }
}
