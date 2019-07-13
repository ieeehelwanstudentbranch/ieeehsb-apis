<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SendTask extends Model
{
    public $table = 'send_tasks';

    public function deliverTasks(){
        return $this->hasMany(DeliverTask::class , 'task_id');
    }
}
