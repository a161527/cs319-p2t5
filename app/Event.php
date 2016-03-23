<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    //
    public $timestamps = false;
    protected $fillable = array('eventName', 'date','location','time','seatsCount','conferenceID');

    public function attendees() {
        return $this->hasMany("App\UserEvent", "eventID", "id");
    }

    public function conference() {
        return $this->hasOne("App\Conference", "id", "conferenceID");
    }
}
