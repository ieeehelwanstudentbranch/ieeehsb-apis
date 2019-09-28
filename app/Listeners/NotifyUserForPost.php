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

        $user = $event->post->user;
        $from_data = [
            'id' => $user->id ,
            'first_name' => $user->firstName,
            'last_name' => $user->lastName ,
            'image' => $user->image ,
        ];
        $notification->from = $from_data;
        $notification->to = null;
        $notification->content ='added a new post';
        $notification->link_to_view = action('PostController@show', $event->post->id);
        $notification->parent_id = $event->post->id;
        $notification->save();
        PusherHandler::send($notification,'notification','post-created');
    }
}
