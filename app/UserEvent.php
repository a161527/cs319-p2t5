<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserEvent extends Model
{
    public $timestamps = false;
    protected $fillable = ['id', 'userID', 'eventID'];

    public function user() {
        return $this->hasOne('App\User', 'id', 'userID');
    }

    public function event() {
        return $this->hasOne('App\Event', 'id', 'eventID');
    }
}
