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
            'from' => new NotificationUserCollection($this->user),
            'to' => $this->to,
            'content' => $this->content,
            'view' => $this->link_to_view,
            'parent_id' => $this->parent_id,
            'created_at' => $this->created_at->toDateTimeString()
        ];
    }
}
