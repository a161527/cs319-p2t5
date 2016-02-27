<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    //
    public $timestamps = false;
    protected $fillable = array('eventName', 'date','location','time','seatsCount','conferenceID');
}
