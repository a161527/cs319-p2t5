<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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

    protected $dates = ['deleted_at'];
}
