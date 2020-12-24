<?php

namespace App\Http\Resources\Award;
use App\Chapter;
use App\Http\Resources\Chapter\ChapterCollection;

use Illuminate\Http\Resources\Json\JsonResource;

class AwardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $chapter = Chapter::findOrFail($this->to);
        
         return [
            'id' => $this->id,
            'information' => $this->body,
            'date'=> $this->date->toDateTimeString(),
            'location' =>$this->location,
            'image' => $this->image,
            'to' => Chapter::find($this->to) != null ? $chapter->name : "Branch"
        ];
    }
}
