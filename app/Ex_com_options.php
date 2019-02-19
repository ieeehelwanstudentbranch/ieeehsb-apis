<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ex_com_options extends Model
{
    public $table = 'ex_com_options';
    public $created_at=false;
    public $updated_at=false;

    public function user(){
        return$this->belongsTo(User::class);
    }
}
