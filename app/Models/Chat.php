<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Chat extends Model
{

    protected $guarded = [];

    protected $casts = [
        'read_at' => 'datetime'
    ];
    public function message(){
        return $this->belongsTo('App\Models\Message');
    }
    public function session(){
        return $this->belongsTo('App\Models\Session');
    }

    public function markAsRead(){
        $this->update([
            'read_at' => Carbon::now()
        ]);
    }
}
