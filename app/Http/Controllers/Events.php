<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class Events extends Controller
{
    //
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($id = null) {
        if ($id == null) {
            return Event::orderBy('id', 'asc')->get();
        } else {
            return $this->show($id);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     *('id', 'eventName', 'date','location','time','seatsCount','conferenceID');
     */
    public function store(Request $request) {
        $event = new Event;

        $event->eventName = $request->input('eventName');
        $event->date = $request->input('date');
        $event->location = $request->input('location');
        $event->time = $request->input('time');
        $event->seatsCount = $request->input('seatsCount');
        $event->conferenceID = $request->input('conferenceID');
        $event->save();

        return 'Employee record successfully created with id ' . $event->id;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        return Event::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id) {
        $event = Event::find($id);

        $event->eventName = $request->input('eventName');
        $event->date = $request->input('date');
        $event->location = $request->input('location');
        $event->time = $request->input('time');
        $event->seatsCount = $request->input('seatsCount');
        $event->conferenceID = $request->input('conferenceID');
        $event->save();

        return "User with Id number " . $event->id ." has been updated.";
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        $event = Event::find($id);
        $event->delete();

        return "Event with ID number " . $id." has been deleted.";
    }
}
