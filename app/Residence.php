<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Residence extends Model
{
    protected $fillable = ["name", "location", "conferenceID"];
    public $timestamps = false;
}
