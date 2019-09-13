<?php

namespace App\Http\Resources\Notification;

use App\Notification;
use Illuminate\Http\Resources\Json\Resource;

class NotificatiosCollection extends Resource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'view' => $this->link_to_view,
            'created_at' => $this->created_at->diffForHumans(),
            'from' => new NotificationUserCollection($this->user),
        ];
    }
}
