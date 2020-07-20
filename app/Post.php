<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['body','status_id','creator'];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function comments(){
        return $this->hasMany('App\Comment');
//        return $this->hasMany('App\Comment');
    }
    public function post()
    {
        return $this->morphTo();
    }
    public function status()
    {
        return $this->hasOne('App\Status','id','status_id');
    }
}
