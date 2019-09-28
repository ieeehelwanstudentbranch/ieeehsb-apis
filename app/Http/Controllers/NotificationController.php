<?php

namespace App\Http\Controllers;

use App\Http\Resources\Notification\NotificatiosCollection;
use App\Notification;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    public function getNotification()
    {
        $notifications = Notification::query()->where(function ($q) {
            $q->where('to', JWTAuth::parseToken()->authenticate()->id)
                ->orWhere('to', null);
        })->get();
        $notification_temp = [];
        foreach ($notifications as $notification) {
            $notification_temp[] = new NotificatiosCollection($notification);
        }

        return $notification_temp;

    }
}
