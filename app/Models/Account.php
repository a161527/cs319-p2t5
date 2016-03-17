<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class Account extends Authenticatable implements JWTSubject
{
    /**
     * This will enable the relation with Role and add the following methods roles(), hasRole($name),
     * can($permission), and ability($roles, $permissions, $options) within your User model.
     *
     */
    use EntrustUserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getJWTIdentifier() {
        return $this->id;
    }

    public function getJWTCustomClaims() {
        return [];
    }
}
