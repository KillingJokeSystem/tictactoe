<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Games extends Model{

    public $timestamps = false;
/*
    public function players(){
	return $this->hasMany('App/Players');
    }
    
    public function moves(){
	return $this->hasMany('App/Moves');
    }
*/  
}
