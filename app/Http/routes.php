<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::auth();

Route::get('/home', 'HomeController@index');
Route::get('/game', ['middleware' => 'auth', function(){
    return view("game");
}]);
Route::get('/move/{pos}',  ['middleware' => 'auth', 'uses' => 'MoveController@send_position']);
Route::get('/get_game',  ['middleware' => 'auth', 'uses' => 'MoveController@get_game']);
Route::get('/check_server',  ['middleware' => 'auth', 'uses' => 'MoveController@check_status']);

Route::get('/create_game/{mm_id}', ['middleware' => 'auth', 'uses' => 'CreationController@create_game']);
Route::get('/join_game/{mm_id}', ['middleware' => 'auth', 'uses' => 'CreationController@join_game']);
Route::get('/create_matchmaking/{size}/{win_condition}/{turn_timeout}', ['middleware' => 'auth', 'uses' => 'CreationController@create_matchmaking']);
Route::get('/get_matchmaking/{size}/{win_condition}/{turn_timeout}', ['middleware' => 'auth', 'uses' => 'CreationController@get_matchmaking']);
Route::get('/update_heart_beat/{mm_id}', ['middleware' => 'auth', 'uses' => 'CreationController@update_heart_beat']);
Route::get('/get_heart_beat/{mm_id}', ['middleware' => 'auth', 'uses' => 'CreationController@get_heart_beat']);
