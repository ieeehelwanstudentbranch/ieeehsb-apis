<?php

namespace App\Http\Resources\Notification;

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
                'firstName' => $this->firstName,
                'lastName' => $this->lastName,
                'image' => $this->image,
            ];

    }
}
