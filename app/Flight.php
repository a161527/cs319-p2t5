<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'id',
        'flightNumber',
        'airline',
        'arrivalDate',
        'arrivalTime',
        'airport',
        'isChecked'];

    public function userConferences() {
        return $this->hasMany('App\UserConference', 'flightID', 'id');
    }

    public function getUserCountAttribute() {
        return $this->userConferences()->count();
    }
}
