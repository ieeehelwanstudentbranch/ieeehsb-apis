<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Status;

class Volunteer extends Model
{
  protected $table = 'volunteers';

  public function position()
  {
    return $this->hasOne(Position::class,'id','position_id');
  }

  public function committee(){
      return $this->belongsToMany(Committee::class,'vol_committees','vol_id')->withPivot('committee_id','position');
  }
  public function comment(){
      return $this->hasMany(Comment::class);
  }
  public function post(){
      return $this->hasMany(Post::class);
  }
  public function status(){
      return $this->belongsTo(Status::class);
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
