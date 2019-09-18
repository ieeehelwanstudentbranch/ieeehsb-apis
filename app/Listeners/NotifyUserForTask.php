<?php

namespace App\Listeners;

use App\Events\TaskEvent;
use App\Lib\RealTime\Pusher\PusherHandler;
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
        if ($event->key == 'send')
        {
            $from = User::query()->findOrFail($event->task->from);
            $from_name = $from->firstName .' '.$from->firstName;

//        foreach ($event->to as $to_user)
//            $to[] = $to_user;

            $notification->from = $event->task->from;
            $notification->to = $event->task->to;
            $notification->content = 'You have received task from '.$from_name .' at ' .  now();
            $notification->link_to_view = action('TaskController@viewTask', $event->task->id);
            $notification->save();
        } elseif ($event->key == 'deliver')
        {
            $from = User::query()->findOrFail($event->task->to);
            $from_name = $from->firstName .' '.$from->firstName;
            $notification->from = $event->task->to;
            $notification->to = $event->task->from;
            $notification->content = $from_name . ' deliver task ' . ' at ' . now();
            $notification->link_to_view = action('TaskController@viewTask', $event->task->id);
            $notification->save();
        } elseif ($event->key == 'refuse-task')
        {
            $from = User::query()->findOrFail($event->task->from);
            $from_name = $from->firstName .' '.$from->firstName;
            $notification->from = $event->task->from;
            $notification->to = $event->task->to;
            $notification->content = 'Your task ' . $event->task->title . ' refused by '. $from_name . ' at ' .now();
            $notification->link_to_view = action('TaskController@viewTask', $event->task->id);
            $notification->save();
        } elseif ($event->key == 'accept-task')
        {
            $from = User::query()->findOrFail($event->task->from);
            $from_name = $from->firstName .' '.$from->firstName;
            $notification->from = $event->task->from;
            $notification->to = $event->task->to;
            $notification->content = 'Your task ' . $event->task->title . ' accepted by '. $from_name . ' at ' .now();
            $notification->link_to_view = action('TaskController@viewTask', $event->task->id);
            $notification->save();
        }
        PusherHandler::send($notification ,'notification' ,'task-created');
    }
}
