<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserConference extends Model
{
    public $timestamps = false;
    protected $fillable = ['id', 'userID', 'conferenceID', 'flightID', 'needsTransportation', 'needsAccommodation', 'approved'];

    public function flight() {
        return $this->hasOne('App\Flight', 'id', 'flightID');
    }

    public function user() {
        return $this->hasOne('App\User', 'id', 'userID');
    }

    public function room() {
        return $this->belongsTo('App\UserRoom', 'id', 'registrationId');
    }
}
