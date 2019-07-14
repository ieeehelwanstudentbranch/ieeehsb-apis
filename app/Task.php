<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    public $table = 'tasks';

    public function user(){
        return $this->belongsTo(User::class);
    }
}
