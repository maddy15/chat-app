<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'type' => $this->type,
            'messages' => $this->message,
            'send_at' => $this->created_at->diffForHumans(),
            'read_at' => $this->read_at ? $this->read_at->diffForHumans() : ''

        ];
    }

    public function read_at_timing(){
        $this->type == 0 ? $this->read_at : null;
        
    }
}
