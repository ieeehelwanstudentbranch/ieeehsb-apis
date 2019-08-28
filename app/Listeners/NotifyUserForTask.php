<?php

namespace App\Listeners;

use App\Events\TaskEvent;
use App\Notification;
use Pusher\Pusher;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyUserForTask
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(TaskEvent $event)
    {
        $notification = new Notification();
        $from = User::query()->findOrFail($event->message->from);
        $from_name = $from->firstName .' '.$from->firstName;

//        foreach ($event->to as $to_user)
//            $to[] = $to_user;

        $notification->from = $event->message->from;
        $notification->to = $event->message->to;
        $notification->content = 'You received task from '.$from_name .' ' . $event->message->created_at;
        $notification->link_to_view = action('TaskController@viewTask', $event->message->id);
        $notification->save();
    }
}
