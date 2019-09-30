<?php

namespace App\Http\Resources\Notification;

use App\Notification;
use Illuminate\Http\Resources\Json\Resource;

class NotificationUserCollection extends Resource
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
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'image' => $this->image,
        ];
    }
}
