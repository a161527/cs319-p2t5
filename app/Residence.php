<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Residence extends Model
{
    protected $fillable = ["name", "location", "conferenceID"];
    public $timestamps = false;

    public function roomSets() {
        return $this->belongsTo('App\RoomSet', 'id', 'residenceID');
    }
}
