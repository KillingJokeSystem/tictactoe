var matchmaking = {
    mm_id:-1,
    game_id:-1,
    s_check:"",
    grid_size:0,
    win_condition:0,
    turn_timeout:0
}

var game = {
    grid_size:3,
    win_condition:3,
    turn_timeout:10,
    turn:0,
    ended:0
}

var player = {
    id:0,
    first:0,
    turn:0,
    lock:0
}

s_check = "";

function InitGame(){

    container = $("#container");
    if( container.length == 0 ) return 0;

    $.ajax({
            url : '/get_game',
            success : function(data){
                data = JSON.parse(data);
                if( data["response"] == 1 ){

                    data       = data["data"];
                    gameData   = data["game"];
                    playerData = data["player"];
                    movesData  = data["moves"];

                    game.grid_size     = gameData["grid_size"];
                    game.win_condition = gameData["win_condition"];
                    game.turn_timeout  = gameData["turn_timeout"];
                    game.ended         = gameData["ended"];
                    game.turn          = movesData.length;

                    player.id    = playerData["id"];
                    player.first = playerData["first"];
		    if( game.turn == 0 & player.first != 1 ){
			player.turn = 1;
		    }
		    generate_table();
		    set_grid_values(movesData);
		    check_server();

		    s_check = setInterval(function(){
		       check_server();
		    }, 1000);
                }
                else{
		    genMatchMaking();
                }
            }
        })
}

function genMatchMaking(){
    form = '<form id="join_game">';
    form += '<div>grid size     : </div><input id="grid_size" type="number" name="grid_size" value="3" ><br/>';
    form += '<div>win condition : </div><input id="win_condition" type="number" name="win_condition" value="3" ><br/>';
    form += '<div>turn timeout  : </div><input id="turn_timeout" type="number" name="turn_timeout" value="30" ><br/>';
    form += '<button id="find_game" type="button">Find a game.</button>';
    form += '<div id="wait_game" style="display:none;" >Waiting for game...</div>';
    form += '</form>';
    $("#container").empty();
    $("#container").append(form);
    $( "#find_game" ).click(function() {
         find_game();
     });

}

function find_game(){
    $("#find_game").prop("disabled",true);
    $("#grid_size").prop("disabled",true);
    $("#win_condition").prop("disabled",true);
    $("#turn_timeout").prop("disabled",true);
    $("#wait_game").show();
    matchmaking.grid_size = $("#grid_size").val();
    matchmaking.win_condition = $("#win_condition").val();
    matchmaking.turn_timeout = $("#turn_timeout").val();
    $.ajax({
        url : "/get_matchmaking/"+matchmaking.grid_size+"/"+matchmaking.win_condition+"/"+matchmaking.turn_timeout,
        success : function(data){
        data = JSON.parse(data);
	if( data["response"] == 1 ){
	    matchmaking.mm_id = data["data"]["id"];
            matchmaking.s_check = setInterval(function(){
                wait_for_game();
            }, 1000);
	}
	else {
            $.ajax({
                url : "/create_matchmaking/"+matchmaking.grid_size+"/"+matchmaking.win_condition+"/"+matchmaking.turn_timeout,
                success : function(data){
	            data = JSON.parse(data);
                    if( data["response"] == 1 ){
  		        matchmaking.mm_id = data["mm_id"];
		        matchmaking.s_check = setInterval(function(){
                               wait_for_match();
                            }, 1000);
	            }
	        }
            });
	}
	}
    });
}

function wait_for_game(){
    $.ajax({
        url : "/get_heart_beat/"+matchmaking.mm_id,
        success : function(data){
            data = JSON.parse(data);
            if( data["response"] == 1 & data["data"]["games_id"] != -1){
                clearInterval(matchmaking.s_check);
		$.ajax({
		    url : "/join_game/"+matchmaking.mm_id,
		    success : function(data){
			InitGame();
		    }
		});
            }
        }
    });
}

function wait_for_match(){
    $.ajax({
        url : "/update_heart_beat/"+matchmaking.mm_id,
        success : function(data){
            data = JSON.parse(data);
            if( data["response"] == 1 & data["data"]["matched"] == 1){
		clearInterval(matchmaking.s_check);
		$.ajax({
		    url : "/create_game/"+matchmaking.mm_id,
		    success : function(data){
			InitGame();
		    }
		});
            }
        }
    });
}

function generate_table(){
    table='<table id="tictactoe">';
    for (i = 1; i <= game.grid_size; i++) {
        table+='<tr id="line'+i+'" class="line" val="'+i+'">';
        for (j = 1; j <= game.grid_size; j++) {
	    table+='<th id="column'+i+'-'+j+'" class="column" val="'+j+'">';
  	    table+='</th>';
        }
        table+='</tr>';
     }
     table+='</table>';
     $("#container").empty();
     $("#container").append(table);

     $( "th" ).click(function() {
         select_box(this);
     });

}

function set_grid_values(moves){
    for (i = 0; i < moves.length; i++){
	move = moves[i];
	game.turn = move["turn"];
        check_grid($("#column"+move["y"]+"-"+move["x"]));
    }
}

function check_grid(box){
    if( game.turn%2 == 0 ){
	$(box).addClass("cross");
    }
    else if ( game.turn%2 == 1 ){
	$(box).addClass("circle");
    }
}

function select_box(box){
    if( player.turn == 1 & !$(box).hasClass("cross") & !$(box).hasClass("circle") & player.lock == 0){
	player.lock = 1;
	x=$(box).attr("val");
	y=$(box).parent().attr("val");
	data=x+":"+y+";";
	$.ajax({
	    url : '/move/'+data,
	    success : function(data){
		data = JSON.parse(data);
		data = data["data"];
		game.turn = data["turn"];
		player.turn = data["is_player_turn"];
		set_player_turn();
		check_server();
		player.lock = 0;
	    }
	});
    }
}

function set_player_turn(){
    if( player.turn == 1 ){
	$("#turnContainer").empty()
	$("#turnContainer").append("<h1>Your Turn<h1>")
    }
    else{
	$("#turnContainer").empty()
	$("#turnContainer").append("<h1>Opponent Turn<h1>")
    }
}

function end_game( winning_play ){
    if( winning_play == 1 & player.turn == 0 ) {
	$("#turnContainer").empty();
	$("#turnContainer").append("<h1>Victory <a href='/game' class='quit' >Quit</a></h1>");
	game.ended = 1;
    }
    else if( winning_play == 1 & player.turn == 1 ) {
	$("#turnContainer").empty();
	$("#turnContainer").append("<h1>You loose <a href='/game' class='quit' >Quit</a></h1>");
	game.ended = 1;
    }
}

function check_server(){
    if ( game.ended == 1 ) clearInterval(s_check);
    $.ajax({
            url : '/check_server/',
            success : function(data){
                data = JSON.parse(data);
		if( data["response"] == 1 ){
		    data = data["data"];
		    game.turn = data["turn"];
		    check_grid($("#column"+data["y"]+"-"+data["x"]));
		    player.turn = data["is_player_turn"];
		    set_player_turn();
		    end_game( data["winning_play"] )
		}
		else if( data["response"] == 0 ){
		    game.turn = 0;
		    set_player_turn();
		}
            }
        });
    return 0;
}

InitGame();

