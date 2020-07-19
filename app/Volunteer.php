<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Volunteer extends Model
{
  protected $table = 'volunteers';

  public function position()
  {
    return $this->hasOne(Position::class,'id','position_id');
  }
 
  public function committee(){
      return $this->belongsTo(Committee::class);
  }
  public function comment(){
      return $this->hasMany(Comment::class);
  }
  public function post(){
      return $this->hasMany(Post::class);
  }
  public function task(){
      return $this->hasMany(Task::class);
  }
   public function user(){
      return $this->belongsTo(User::class);
  }
  public function hasPos($position)
{
  $pos = Position::where('name',$position)->first();
    return Volunteer::where('position_id', $pos->id)->get();
}
}
