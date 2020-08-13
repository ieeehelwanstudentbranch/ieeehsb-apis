<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
  protected $fillable = [
    'name', 'role_id'
  ];
  public function role()
  {
    return $this->belongsTo(Role::class);
  }
}
