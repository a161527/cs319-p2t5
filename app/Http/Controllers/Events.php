<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class Events extends Controller
{
    /**
     * Display a listing of all events in the database.
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
     * Display a listing of events given the conferenceID.
     * @return Response
     */
    public function getEventByConferenceID($conferenceID) {
    return Event::where('conferenceID',$conferenceID)->get();
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request) {
        $event = new Event;
        $event->eventName = $request->input('eventName');
        $event->date = $request->input('date');
        $event->location = $request->input('location');
        $event->startTime = $request->input('startTime');
        $event->endTime = $request->input('endTime');
        $event->capacity = $request->input('capacity');
        $event->description = $request->input('description');
        $event->conferenceID = $request->input('conferenceID');
        $event->save();
        return response()->json(['id' => $event->id]);
    }

    /**
     * Display the event given the eventID.
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        return Event::find($id);
    }

    /**
     * Update the specified event give the eventID.
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id) {
        $event = Event::find($id);
        $event->eventName = $request->input('eventName');
        $event->date = $request->input('date');
        $event->location = $request->input('location');
        $event->startTime = $request->input('startTime');
        $event->endTime = $request->input('endTime');
        $event->capacity = $request->input('capacity');
        $event->description = $request->input('description');
        $event->conferenceID = $request->input('conferenceID');
        $event->save();
        return response()->json(['id' => $event->id]);
    }

    /**
     * Remove the event given the eventID.
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        $event = Event::find($id);
        $event->delete();
        return response()->json(['id' => $event->id]);
    }
}
