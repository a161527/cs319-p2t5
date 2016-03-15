<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoomSet extends Model
{
    protected $fillable =
        ["name", "rangeStart", "rangeEnd", "residenceID", "typeID"];
    public $timestamps = false;
}
