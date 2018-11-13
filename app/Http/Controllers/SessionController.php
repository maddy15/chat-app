<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Session;
use App\Http\Resources\SessionResource;
use App\Events\SessionEvent;

class SessionController extends Controller
{
   public function create(Request $request){
       
       $session = Session::create([
           'user1_id' => auth()->user()->id,
           'user2_id' => $request->friend_id
       ]);
       broadcast(new SessionEvent(new SessionResource($session),auth()->id()));
       return new SessionResource($session);
   }
}
