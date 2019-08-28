<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'from', 'to', 'content','link_to_view'
    ];
    public function user(){
        return $this->belongsTo(User::class,'from');
    }
}
