<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserConference extends Model
{
    public $timestamps = false;
    protected $fillable = ['id', 'userID', 'conferenceID', 'flightID', 'needsTransportation', 'approved'];

    public function flight() {
        return $this->hasOne('App\Flight', 'id', 'flightID');
    }
}
