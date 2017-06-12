<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Players extends Model{

    public $timestamps = false;
/*
    public function games(){
        return $this->belongsTo('App/Games');
    }

    public function moves(){
	return $this->hasMany('App/Moves');
    }
*/  
}
