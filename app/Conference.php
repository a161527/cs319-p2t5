<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conference extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id',
        'conferenceName',
        'dateStart',
        'dateEnd',
        'location',
        'description',
        'hasTransportation',
        'hasAccommodations'];
}
