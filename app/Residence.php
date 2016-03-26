<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Residence extends Model
{
    protected $fillable = ["name", "location", "conferenceID"];
    public $timestamps = false;

    public function roomSets() {
        return $this->hasMany('App\RoomSet', 'residenceID', 'id');
    }

    public function conference() {
        return $this->hasOne('App\Conference', 'id', 'conferenceID');
    }
}
