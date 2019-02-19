<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HighBoardOptions extends Model
{
    public $table = 'high_board_options';
    public $created_at=false;
    public $updated_at=false;

    public function user(){
        return$this->belongsTo(User::class);
    }
}
