<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Transportation;
use App\conference;
use App\Flight;
use DB;
use Validator;

class TransportationController extends Controller
{
	private function transportValidator(array $data)
	{
        $validator = Validator::make($data, [
            '*.capacity'		=>	'required|numeric|min:1|max:100',
            '*.name'			=>	'required|max:255',
            '*.company'			=>	'required|max:255',
            '*.phone'			=>	'required',
            '*.conferenceID'	=>	'required|numeric'
        ]);

        return $validator;
	}

	private function transportEditValidator(array $data)
	{
        $validator = Validator::make($data, [
            'capacity'		=>	'numeric|min:1|max:100',
            'name'			=>	'max:255',
            'company'		=>	'max:255',
            'phone'			=>	'',
            'conferenceID'	=>	'numeric'
        ]);

        return $validator;
	}

	private function insertTransport($data)
	{
        $transport = new Transportation();
        $transport->capacity = $data['capacity'];
        $transport->name = $data['name'];
        $transport->company = $data['company'];
        $transport->phone = $data['phone'];
        $transport->conferenceID = $data['conferenceID'];
        if (isset($data['flightID']))
        	$transport->flightID = $data['flightID'];
        
        $transport->save();
	}

    /*
     * GET /api/transportation [?confID=123 & flightID=123]
     * - gets a list of transportations 
     * confID and flightID optional
     */
    public function index(Request $req) {
    	$confId = isset($req->all()['confID']) ? $req->all()['confID'] : null;
    	$flightId = isset($req->all()['flightID']) ? $req->all()['flightID'] : null;

    	if ($confId && $flightId)
    		$transport = Transportation::where('conferenceID', $confId)->where('flightID', $flightId)->get();
    	else if ($confId)
    		$transport = Transportation::where('conferenceID', $confId)->get();
    	else if ($flightId)
    		$transport = Transportation::where('flightID', $flightId);
    	else
    		$transport = Transportation::all();
        
        return response()->json(['message' => 'returned_transportation', 'transportation' => $transport]);
    }

    /*
     * POST /api/transportation/
     * - adds a list of transportation objects to the db
     * takes [{...},{...}]
     * object params: capacity, name, company, phone, conferenceID, flightID (optional)
     */
    public function addTransport(Request $req) {
        $transports = $req->all();
        try
        {   
            DB::beginTransaction();
            $validator = $this->transportValidator($transports);
            if ($validator->passes())
            {
	            foreach ($transports as $t)
	            {
	                $this->insertTransport($t);    
	            }
	        }
	        else
                return response()->json(['message' => 'validation_failed', 'errors' => $validator->errors()], 422);
            DB::commit();
            return response()->json(['message' => 'transports_added']);
        } catch (ValidationException $e) {
            DB::rollback();
            return response()->json(['message' => 'transport_could_not_be_added', 'errors' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'unknown_error', 'errors' => $e->getMessage()], 500);
        }
    }

    /*
     * DELETE /api/transportation/{transportationId}
     * - deletes a transportation
     */
    public function deleteTransport($transportId) {
		$transport = Transportation::where('id', $transportId);
        if ($transport->delete())
            return response()->json(['message' => 'transportation_deleted'], 200);
        else
            return response()->json(['message' => 'transportation_could_not_be_deleted'], 500);
    }

    /*
     * PATCH /api/transportation/{transportationId}
     * - edit a transportation
     * same object format as insertion, but only takes a single object of changes to be made (not a list)
     */
    public function patchTransport($transportId, Request $req) {
        $changes = $req->all();
        $transport = Transportation::where('id', $transportId)
                    ->first();
        if (!$transport)
            return response()->json(['message' => 'transportation_does_not_exist'], 422);
        else 
        {
            $validator = $this->transportEditValidator($changes);
            if ($validator->passes())
                // do hash_update_stream(context, handle)
                foreach ($changes as $field => $newValue)
                {
                    switch($field)
                    {
                        case "capacity":
                            $transport->capacity = $newValue;
                            break;
                        case "name":
                            $transport->name = $newValue;
                            break;
                        case "phone":
                            $transport->phone = $newValue;
                            break;
                        case "company":
                            $transport->company = $newValue;
                            break;
                        case "conferenceID":
                            $transport->conferenceID = $newValue;
                            break;
                        case "flightID":
                            $transport->flightID = $newValue;
                            break;
                    }
                }
            else
                return response()->json(['message' => 'validation_failed', 'errors' => $validator->errors()], 422);
        }

        if ($transport->save())
            return response()->json(['message' => 'transportation_updated'], 200);
        else
            return response()->json(['message' => 'transportation_could_not_be_updated'], 500);
    }


    /*
     * POST /api/transportation/{transportationId}/assignFlight
     * - assigns a transportation to a flight
     * takes {flightId: 1}
     */
    public function assignFlight($transportId, $flightId) {
    	$flight = Flight::where('id', '=', $flightId)->first();
        if ($flight === null)
            return response()->json(['message' => 'flight_not_found'], 404);

    	$transport = Transportation::where('id', $transportId);
    	$transport->flightID = $flightId;
    	$transport->save();
    }
}
