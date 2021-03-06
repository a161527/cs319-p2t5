<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'id',
        'firstName',
        'lastName',
        'dateOfBirth',
        'gender',
        'location',
        'notes',
        'accountID'];

    public function account() {
        return $this->hasOne('App\Models\Account', 'id', 'accountID');
    }

    public function registrations() {
        return $this->hasMany('App\UserConference', 'userID', 'id');
    }

    protected $dates = ['deleted_at'];
}
