<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Chat extends Model
{

    protected $guarded = [];

    public function message(){
        return $this->belongsTo('App\Models\Message');
    }

    public function markAsRead(){
        $this->update([
            'read_at' => Carbon::now()
        ]);
    }
}
