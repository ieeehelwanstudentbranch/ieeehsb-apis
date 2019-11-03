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
            $user = User::query()->findOrFail($event->task->from);
            $from_data = [
                'id' => $user->id ,
                'first_name' => $user->firstName,
                'last_name' => $user->lastName ,
                'image' => $user->image ,
            ];
            $notification->from = $from_data;
            $notification->to = $event->task->to;
            $notification->content = 'You have received a new task';
            $notification->link_to_view = action('TaskController@viewTask', $event->task->id);
            $notification->parent_id = $event->task->id;
            $notification->save();
        } elseif ($event->key == 'deliver')
        {
            $user = User::query()->findOrFail($event->task->to);
            $from_data = [
                'id' => $user->id ,
                'first_name' => $user->firstName,
                'last_name' => $user->lastName ,
                'image' => $user->image ,
            ];
            $notification->from = $from_data;
            $notification->to = $event->task->from;
            $notification->content = 'has delivered his task';
            $notification->link_to_view = action('TaskController@viewTask', $event->task->id);
            $notification->parent_id = $event->task->id;
            $notification->save();
        } elseif ($event->key == 'refuse-task')
        {
            $user = User::query()->findOrFail($event->task->from);
            $from_data = [
                'id' => $user->id ,
                'first_name' => $user->firstName,
                'last_name' => $user->lastName ,
                'image' => $user->image ,
            ];
            $notification->from = $from_data;
            $notification->to = $event->task->to;
            $notification->content = 'Your task ' . $event->task->title . ' has been refused';
            $notification->link_to_view = action('TaskController@viewTask', $event->task->id);
            $notification->parent_id = $event->task->id;
            $notification->save();
        } elseif ($event->key == 'accept-task')
        {
            $user = User::query()->findOrFail($event->task->from);
            $from_data = [
                'id' => $user->id ,
                'first_name' => $user->firstName,
                'last_name' => $user->lastName ,
                'image' => $user->image ,
            ];
            $notification->from = $from_data;
            $notification->to = $event->task->to;
            $notification->content = 'Congratulations, Your task ' . $event->task->title . ' has been accepted';
            $notification->link_to_view = action('TaskController@viewTask', $event->task->id);
            $notification->parent_id = $event->task->id;
            $notification->save();
        }
        PusherHandler::send($notification ,'notification' ,'task-created');
    }
}
