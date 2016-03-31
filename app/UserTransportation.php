<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserTransportation extends Model
{
    protected $table = 'user_transportation';
    public $timestamps = false;

    public function transportation() {
        return $this->hasOne('App\Transportation', 'id', 'transportationID');
    }

    public function userConference() {
        return $this->hasOne('App\UserConference', 'id', 'userconferenceID');
    }
}
