<?php

namespace App\Http\Controllers\Conference;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class RoomSetupController extends Controller
{
    public function __construct() {
        $this->middleware('jwt.auth');
    }

    public function getRoomList($confId) {

    }
}
