<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
  protected $table = 'roles';
    public function volunteer()
  {
    return $this->hasOneThrough('App\Volunteer', 'App\Position');
  }
   public function position()
  {
    return $this->hasMany('App\Position','role_id','id');
  }
}
