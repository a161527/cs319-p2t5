<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    /**
     * This will enable the relation with Role and add the following methods roles(), hasRole($name), 
     * can($permission), and ability($roles, $permissions, $options) within your User model.
     * 
     */
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'firstName',
        'lastName',
        'dateOfBirth',
        'gender',
        'location',
        'notes',
        'accountID'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function account() {
        return $this->hasOne('App\Models\Account', 'id', 'accountID');
    }
}
