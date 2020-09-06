<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
	    public $table = 'chapters';
        protected $fillable = ['name','description','logo','chairperson_id'];


    public function committee()
    {
    	return $this->hasMany(Committee::class,'chapter_id');
    }
     public function post()
    {
        return $this->morphMany('App\Post', 'post');
    }
}
