<?php

namespace App;
use App\Committee;
use App\Chapter;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
	    public $table = 'chapters';

    public function committee()
    {
    	return $this->hasMany(Committee::class,'chapter_id');
    }
     public function post()
    {
        return $this->morphMany('App\Post', 'post');
    }
}
