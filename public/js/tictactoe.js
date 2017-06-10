var turn = 1;

function generate_table(size){
    table='<table id="tictactoe">';
    for (i = 0; i < size; i++) {
	table+='<tr id="line'+i+'" class="line">';
	for (j = 0; j < size; j++) {
	table+='<th id="column'+j+'" class="column">';
	table+='</th>';
	}
	table+='</tr>';
    }
    table+='</table>';
    $("#container").append(table);
}

generate_table(3);

$( "th" ).click(function() {
    if( turn%2 == 1 ){
	$(this).addClass("cross");
    }
    else if ( turn%2 == 0 ){
	$(this).addClass("circle");
    }
    turn += 1;
});
