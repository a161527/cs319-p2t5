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
    $event = Event::find($conferenceID);
      if (is_null($event)) {
          return response("No events for conferenceID {$conferenceID}.", 404);
      }
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
      $event = Event::find($id);
        if (is_null($event)) {
            return response("No event for id {$id}.", 404);
        }
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
        if (is_null($event)) {
            return response("No event for id {$id}.", 404);
        }
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
        if (is_null($event)) {
            return response("No event for id {$id}.", 404);
        }
        $event->delete();
        return response()->json(['id' => $event->id]);
    }
}
