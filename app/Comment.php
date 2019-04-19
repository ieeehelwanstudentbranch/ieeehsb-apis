<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public $table = 'comments';
    public $timestamps =false;

    public function post(){
//        return $this->belongsTo('App\Post');
        return $this->belongsTo(Post::class);
    }
    public function user(){
//        return $this->belongsTo('App\User');
        return $this->belongsTo(User::class);
    }
}
