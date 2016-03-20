<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRoom extends Model
{
    public $timestamps = false;
    public $fillable = ['roomSetID', 'roomName', 'registrationID'];

    public function roomSet() {
        return $this->hasOne('App\RoomSet', 'id', 'roomSetID');
    }

    public function registration() {
        return $this->hasOne('App\UserConference', 'id', 'registrationID');
    }
}
