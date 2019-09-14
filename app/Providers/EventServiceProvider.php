<?php

namespace App\Providers;

use App\Events\PostEvent;
use App\Events\TaskEvent;
use App\Listeners\NotifyUserForPost;
use App\Listeners\NotifyUserForTask;
use App\Notification;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        TaskEvent::class => [
            NotifyUserForTask::class,
        ],
        PostEvent::class => [
            NotifyUserForPost::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
