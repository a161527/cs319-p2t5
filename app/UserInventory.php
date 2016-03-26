<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserInventory extends Model
{
    //
    protected $table = 'user_inventory';
    public $timestamps = false;
    
    /**
     *	Get the conference associated with the inventory entry
     */
    public function user()
    {
    	return $this->hasOne('App\User', 'userID');
    }

    /**
     *	Get the conference associated with the inventory entry
     */
    public function inventory()
    {
    	return $this->hasOne('App\Inventory', 'inventoryID');
    }
}
