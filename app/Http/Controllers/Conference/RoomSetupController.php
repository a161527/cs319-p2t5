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

    public function getAccommodationList($confId) {

    }

    public function uploadRoomData($confId) {

    }

    public function createResidence($confId) {

    }

    public function getResidenceRooms($confId, $residenceId) {

    }

    public function getResidenceRoomTypes($confId, $residenceId) {

    }

    public function createRoomSet($confId, $residenceId) {

    }
}
