<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoomSet extends Model
{
    protected $fillable =
        ["name", "residenceID", "typeID"];
    public $timestamps = false;

    public function type() {
        return $this->hasOne('App\RoomType', 'id', 'typeID');
    }

    public function residence() {
        return $this->hasOne('App\Residence', 'id', 'residenceID');
    }

    public function assignments() {
        return $this->hasMany('App\UserRoom', 'roomSetID', 'id');
    }
}
