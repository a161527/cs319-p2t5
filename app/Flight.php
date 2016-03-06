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
        'airport'];
}
