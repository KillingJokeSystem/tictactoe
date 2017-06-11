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
Route::get('/game', function(){
    return view("game");
});
Route::get('/move/{pos}', 'MoveController@send_position');
Route::get('/get_game', 'MoveController@get_game');
Route::get('/check_server', 'MoveController@check_status');
Route::get('/create_game/{size}/{win_condition}/{turn_timeout}', 'CreationController@create_game');
