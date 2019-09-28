<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'from', 'to', 'content','link_to_view','parent_id' , 'sender_image'
    ];

    protected $casts = [
        'from' => 'array',
    ];
    public function user(){
        return $this->belongsTo(User::class,'from');
    }
}
