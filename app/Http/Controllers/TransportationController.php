<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Transportation;
use App\Conference;
use App\Flight;
use App\UserConference;
use App\User;
use App\UserTransportation;
use DB;
use Validator;

class TransportationController extends Controller
{
    public function __construct()
    {
        // $this->middleware('jwt.auth', ['except' => ['authenticate', 'token']]);
    }
    private function transportValidator(array $data)
    {
        $validator = Validator::make($data, [
            '*.capacity'        =>    'required|numeric|min:1|max:100',
            '*.name'            =>    'required|max:255',
            '*.company'            =>    'required|max:255',
            '*.phone'            =>    'required',
            '*.conferenceID'    =>    'required|numeric'
        ]);

        return $validator;
    }

    private function transportEditValidator(array $data)
    {
        $validator = Validator::make($data, [
            'capacity'        =>    'numeric|min:1|max:100',
            'name'            =>    'max:255',
            'company'        =>    'max:255',
            'phone'            =>    '',
            'conferenceID'    =>    'numeric'
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

    private function buildSummaryJson($conferences, $flights, $userConfs)
    {
        $r = $conferences[0];
        $r['flights'] = $flights;

        foreach ($userConfs as $user)
        {
            $r ['flights'] [$user['flightID']] ['accounts'] [$user['user']['accountID']] ['users'] [] = $user['user'];
        }

        foreach ($r['flights'] as &$fl)
        {
            foreach ($fl['accounts'] as &$acc)
            {
                $acc['count'] = count($acc['users']);
            }
        }

        return $r;
    }

    /*
     * GET /api/transportation [?confID=123 & flightID=123]
     * - gets a list of transportations 
     * confID and flightID optional
     */
    public function index(Request $req) 
    {
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
    public function addTransport(Request $req) 
    {
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
    public function deleteTransport($transportId) 
    {
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
    public function patchTransport($transportId, Request $req) 
    {
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
     * POST /api/transportation/{transportationId}/assignTransport
     * - assigns a transportation to a flight
     * takes {userConferenceIDs: [1,2,...]} (a list of userConferenceID)
     */
    public function assignTransport($transportId, Request $req) 
    {
        $data = $req->all();

        foreach ($data['userConferenceIDs'] as $userconfId)
        {
            $userconf = UserConference::where('id', '=', $userconfId)->first();
            if ($userconf === null)
                return response()->json(['message' => 'userconference_not_found'], 404);
        }

        $transport = Transportation::where('id', '=', $transportId)->first();
        if ($transport === null)
            return response()->json(['message' => 'transportation_not_found'], 404);

        DB::beginTransaction();
        foreach ($data['userConferenceIDs'] as $userconferenceId)
        {
            $ut = new UserTransportation();
            $ut->userconferenceID = $userconferenceId;
            $ut->transportationID = $transportId;
            if (!$ut->save())
            {
                DB::rollback();
                return response()->json(['message' => 'error_assigning_transport'], 500);    
            }
        }
        
        DB::commit();
        return response()->json(['message' => 'transport_assigned'], 200);
    }

    /*
     * GET /api/transportation/summary/
     * - get transportation summary for a conference
     * takes {confID: 1}
     */
    public function transportSummary(Request $req)
    {
        $data = $req->all();
        $confId = isset($data['confID']) ? $data['confID'] : null;
        if (!$confId)
            return response()->json(['message' => 'conference_not_found'], 422);

        $userConfs = UserConference::where('needsTransportation', '=', true)->where('conferenceID', $confId)
                                   ->with(array('user'=>function($q){
                                        $q->select('id','firstName','lastName','accountID');
                                   }));
        // $usersList = $userConfs->distinct()->lists('userID');
        $flightsList = $userConfs->distinct()->lists('flightID');
        // $confsList = $userConfs->distinct()->lists('conferenceID');
        $flights = Flight::whereIn('id', $flightsList)->get()->keyBy('id')->toArray();
        $conference = Conference::where('id', $confId)->get()->toArray();
        
        // return $userConfs->get();
        $summary = $this->buildSummaryJson($conference, $flights, $userConfs->get()->toArray());
        return response()->json($summary, 200);
    }
}
