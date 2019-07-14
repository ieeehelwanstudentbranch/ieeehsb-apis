<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Committee extends Model
{
    public $table = 'committees';
    public $created_at= false;
    public $updated_at=false;


    public function user(){
        return$this->hasMany(User::class);
    }
}
