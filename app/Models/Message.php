<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $guarded = [];

    public function chats(){
        return $this->hasMany('App\Models\Chat');
    }

    public function createForSend($session_id){
        return $this->chats()->create([
            'session_id' => $session_id,
            'type' => 0,
            'user_id' => auth()->id()
        ]);
    }

    public function createForRecieve($session_id,$to_user){
        return $this->chats()->create([
            'session_id' => $session_id,
            'type' => 1,
            'user_id' => $to_user
        ]);
    }
}
