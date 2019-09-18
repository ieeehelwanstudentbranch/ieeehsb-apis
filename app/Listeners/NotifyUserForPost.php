<?php

namespace App\Listeners;

use App\Events\PostEvent;
use App\Events\TaskEvent;
use App\Lib\RealTime\Pusher\PusherHandler;
use App\Notification;
use Monolog\Handler\PushoverHandler;
use Pusher\Pusher;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyUserForPost
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
    public function handle(PostEvent $event)
    {
        $notification = new Notification();

        $from = $event->post->user;
        $from_name = $from->firstName .' '.$from->firstName;
        $notification->from = $from->id;
        $notification->to = null;
        $notification->content = $from_name .' added a new post at ' .  now();
        $notification->link_to_view = action('PostController@show', $event->post->id);
        $notification->save();
        PusherHandler::send($notification,'notification','post-created');
    }
}
