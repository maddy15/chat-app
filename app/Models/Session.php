<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $guarded = [];
    public function chats(){
        return $this->hasManyThrough('App\Models\Chat','App\Models\Message');
    }

    public function messages(){
        return $this->hasMany('App\Models\Message');
    }
}
