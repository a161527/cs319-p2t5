<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';

    /**
     *	Get the conference associated with the inventory entry
     */
    public function conference()
    {
    	return $this->hasOne('App\Conference', 'conferenceID');
    }
}
