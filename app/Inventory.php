<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';

    /**
     * Get the conference associated with the inventory entry
     */
    public function conference()
    {
        return $this->hasOne('App\Conference', 'conferenceID');
    }

    public function reservations() {
        return $this->hasMany('App\UserInventory', 'inventoryID', 'id');
    }

    public function getCurrentQuantityAttribute() {
        return $this->totalQuantity - $this->reservations()->sum('unitCount');
    }
}
