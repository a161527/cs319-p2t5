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
            '*.company'         =>    'max:255',
            '*.phone'           =>    'required'
        ]);

        return $validator;
    }

    private function transportEditValidator(array $data)
    {
        $validator = Validator::make($data, [
            'capacity'        =>    'numeric|min:1|max:100',
            'name'            =>    'max:255',
            'company'         =>    'max:255',
            'phone'           =>    ''
        ]);

        return $validator;
    }

    private function insertTransport($data, $confId)
    {
        $transport = new Transportation();
        $transport->capacity = $data['capacity'];
        $transport->name = $data['name'];
        $transport->company = $data['company'];
        $transport->phone = $data['phone'];
        $transport->conferenceID = $confId;
        
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
            $fl['count'] = 0;
            foreach ($fl['accounts'] as &$acc)
            {
                $acc['count'] = count($acc['users']);
                $fl['count'] += $acc['count'];
            }

        }

        return $r;
    }

    private function isValidConference($confId)
    {
        $conf = Conference::where('id', '=', $confId)->first();
        if ($conf === null)
            return false;
        return true;
    }

    private function isValidTransport($transportId)
    {
        $r = Transportation::where('id', '=', $transportId)->first();
        if ($r === null)
            return false;
        return true;
    }

    /*
     * GET /api/conferences/{confId}/transportation ?flightID=123 (flightID is optional)
     * - gets a list of transportations 
     * confID and flightID optional
     */
    public function index($confId) 
    {
        if (!($this->isValidConference($confId)))
            return response()->json(['message' => 'conference_not_found'], 404);

        $transport = Transportation::where('conferenceID', $confId)->get();
        return response()->json(['message' => 'returned_transportation', 'transportation' => $transport]);
    }

    /*
     * POST /api/conferences/{confId}/transportation/
     * - adds a list of transportation objects to the db
     * takes [{...},{...}]
     * object params: capacity, name, company (optional), phone, conferenceID
     */
    public function addTransport($confId, Request $req) 
    {
        if (!($this->isValidConference($confId)))
            return response()->json(['message' => 'conference_not_found'], 404);

        $transports = $req->all();
        try
        {   
            DB::beginTransaction();
            $validator = $this->transportValidator($transports);
            if ($validator->passes())
            {
                foreach ($transports as $t)
                {
                    $this->insertTransport($t, $confId);    
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
     * DELETE /api/conferences/{confId}/transportation/{transportId}
     * - deletes a transportation
     */
    public function deleteTransport($confId, $transportId) 
    {
        if (!($this->isValidConference($confId)))
            return response()->json(['message' => 'conference_not_found'], 404);
        if (!($this->isValidTransport($transportId)))
            return response()->json(['message' => 'transportation_not_found'], 404);

        $transport = Transportation::where('id', $transportId)->where('conferenceID', $confId);
        if ($transport->delete())
            return response()->json(['message' => 'transportation_deleted'], 200);
        else
            return response()->json(['message' => 'transportation_could_not_be_deleted'], 500);
    }

    /*
     * PATCH /api/conferences/{confId}/transportation/{transportId}
     * - edit a transportation
     * same object format as insertion, but only takes a single object of changes to be made (not a list)
     */
    public function patchTransport($confId, $transportId, Request $req) 
    {
        if (!($this->isValidConference($confId)))
            return response()->json(['message' => 'conference_not_found'], 404);
        if (!($this->isValidTransport($transportId)))
            return response()->json(['message' => 'transportation_not_found'], 404);

        $changes = $req->all();
        $transport = Transportation::where('id', $transportId)->where('conferenceID', $confId)
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
     * POST /api/conferences/{confId}/transportation/{transportId}/assignTransport
     * - assigns a transportation to a flight
     * takes {userConferenceIDs: [1,2,...]} (a list of userConferenceID)
     */
    public function assignTransport($transportId, Request $req) 
    {
        if (!($this->isValidConference($confId)))
            return response()->json(['message' => 'conference_not_found'], 404);
        if (!($this->isValidTransport($transportId)))
            return response()->json(['message' => 'transportation_not_found'], 404);

        $data = $req->all();

        foreach ($data['userConferenceIDs'] as $userconfId)
        {
            $userconf = UserConference::where('id', '=', $userconfId)->first();
            if ($userconf === null)
                return response()->json(['message' => 'userconference_not_found'], 404);
        }

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
     * POST /api/conferences/{id}/transportation/{transportId}/unassign
     * takes {userIDs:[1,2,3]} (a list of userID)
     */
    public function unassignTransport($confId, $transportId, Request $req)
    {
        $userIDs = $req->input('userIDs');
        if ($userIDs === null)
            return response()->json(['message' => 'userIDs_not_provided'], 422);
        
        DB::beginTransaction();
        foreach ($userIDs as $id)
        {
            $user = User::where('id', $id)->first();
            if ($user === null)
                return response()->json(['message' => 'user_not_found'], 404);
            $uc = UserConference::where('conferenceID', '=', $confId)
                                ->where('userID', $id)
                                ->first();
            $ut = UserTransportation::where('userconferenceID', $uc->id)
                                ->first();
            if ($ut === null)
            {
                DB::rollback();
                return response()->json(['message' => 'usertransportation_not_found'], 404);
            }
            if (!$ut->delete())
            {
                DB::rollback();
                return response()->json(['message' => 'usertransportation_could_not_be_deleted'], 500);
            }
        }
        DB::commit();
        return response()->json(['message' => 'usertransportation_deleted'], 200);
    }

    /*
     * GET /api/conferences/{confId}/transportation/{transportId}
     *
     */
    public function getTransport($confId, $transportId)
    {
        $transport = Transportation::where('id', $transportId)
                                   ->where('conferenceID', $confId)
                                   ->first();
        if ($transport === null)
            return response()->json(['message' => 'transportation_not_found'], 404);
        return response()->json(['message' => 'transportation_returned', 'transportation' => $transport], 200);
    }

    /*
     * GET /api/conferences/{confId}/transportation/summary/
     * - get transportation summary for a conference
     * 
     */
    public function transportSummary($confId, Request $req)
    {
        if (!($this->isValidConference($confId)))
            return response()->json(['message' => 'conference_not_found'], 404);

        $userConfs = UserConference::where('needsTransportation', '=', true)->where('conferenceID', $confId)
                                   ->with(array('user'=>function($q){
                                        $q->select('id','firstName','lastName','accountID');
                                   }))
                                   ->with('userTransportation');

        $flightsList = $userConfs->distinct()->lists('flightID');
        $flights = Flight::whereIn('id', $flightsList)->get()->keyBy('id')->toArray();
        $conference = Conference::where('id', $confId)->get()->toArray();
        
        $summary = $this->buildSummaryJson($conference, $flights, $userConfs->get()->toArray());
        return response()->json($summary, 200);
    }
}
