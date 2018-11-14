<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Session;
use App\Models\Message;
use App\Http\Resources\ChatResource;
use App\Events\PrivateEvent;
use App\Events\MsgReadEvent;

class ChatController extends Controller
{
    public function send(Session $session,Request $request){
        
        $message =  $session->messages()->create([
            'content' => $request->message,
        ]);

        $chat = $message->createForSend($session->id);
        $message->createForRecieve($session->id,$request->to_user);
        
        broadcast(new PrivateEvent($message->content,$chat,$request->to_user));
        
        return ChatResource::collection($message->chats->where('user_id',auth()->id()));
    }

    public function chats(Session $session)
    {
        return ChatResource::collection($session->chats->where('user_id',auth()->id()));
    }

    public function read(Session $session){
        $chats = $session->chats->where('read_at',null)->where('type',0)->where('user_id','!=',auth()->id());

        foreach($chats as $chat){
            $chat->markAsRead();
            broadcast(new MsgReadEvent(new ChatResource($chat),$chat->session_id));
        }
        
    }
}
