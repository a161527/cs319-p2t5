<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    protected $fillable = ["name", "capacity", "accessible"];
    public $timestamps = false;
}
