<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeliverTask extends Model
{
    public $table = 'deliver_tasks';

    public function sendTasks(){
        return $this->belongsTo(SendTask::class);
    }
}
