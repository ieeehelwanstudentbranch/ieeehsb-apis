<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    public $table = 'tasks';

    public function user(){
        return $this->belongsTo(User::class);
    }
//    public function task()
//    {
//        return $this->morphTo();
//    }
    public function status()
    {
        return $this->hasOne('App\Status','id','status_id');
    }

    public function feedback()
    {
        return $this->hasMany(TaskFeedback::class,'task_id','id');
//        return $this->belongsToMany(TaskFeedback::class,'feedback_tasks','task_id');
    }
    public function committee()
    {
        return $this->hasOne('App\Committee','id','comm_id');
    }
}
