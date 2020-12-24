<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskFeedback extends Model
{
    public $table = 'feedback_tasks';
    protected $fillable = ['feedback','feedback_creator'];
    public $timestamps = true;
}
