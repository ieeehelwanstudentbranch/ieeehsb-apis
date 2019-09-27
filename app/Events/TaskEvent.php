<?php
namespace  App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TaskEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $task;
    public $key;

    public function __construct($task , $key)
    {
        $this->task = $task;
        $this->key = $key;
    }

    public function broadcastOn()
    {
        return 'notification';
    }

    public function broadcastAs()
    {
        return 'notification';
    }
}