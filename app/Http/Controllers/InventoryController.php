<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Inventory;
use App\Conference;
use Validator;
use DB;
use ValidationException;

class InventoryController extends Controller
{
    public function __construct()
    {
        // $this->middleware('jwt.auth', ['except' => ['authenticate', 'token']]);
        // provides an authorization header with each response
        // $this->middleware('jwt.refresh', ['except' => ['authenticate', 'token']]);
    }

    /*
     * Get a validator for an incoming item array add request.
     */
    protected function itemValidator(array $data)
    {
        $validator = Validator::make($data, [
            // need to change the name fields to allow accented characters, dashes, apostrophes, etc.
            '*.currentQuantity' => 'required|numeric|min:0',
            '*.totalQuantity' => 'required|numeric|min:0',
            '*.units' => 'required|numeric|min:0',
            // allow alphanum itemName with dashes, brackets,
            '*.itemName' => 'required|regex:/^[a-zA-Z0-9()\s-]+$/',
            '*.disposable' => 'required|boolean'
        ]);

        return $validator;
    }

    /*
     * Insert a validated user into the database
     */
    protected function insertItem($conferenceId, $data)
    {
        $item = new Inventory();
        $item->currentQuantity = $data['currentQuantity'];
        $item->totalQuantity = $data['totalQuantity'];
        $item->units = $data['units'];
        $item->itemName = $data['itemName'];
        $item->disposable = $data['disposable'];
        $item->conferenceID = $conferenceId;
        $item->save();
    }

    /*
     * GET api/conferences/{conferenceId}/inventory
     * - return a list showing the inventory of a conference
     */
    public function index($conferenceId)
    {
        // TODO: add permission/role filter
        $conf = Conference::where('id', '=', $conferenceId)->first();
        if ($conf === null)
            return response()->json(['message' => 'conference_not_found'], 404);
        else
        {
            $inventory = Inventory::where('conferenceID', '=', $conf->id)->get();
            return response()->json(['message' => 'returned_inventory', 'inventory' => $inventory]);
        }
    }

    /*
     * POST api/conferences/{conferenceId}/inventory
     * PUT api/conferences/{conferenceId}/inventory
     * - add an item to a conference's inventory
     */
    public function addItem($conferenceId, Request $req)
    {
        $items = $req->all();
        try
        {   
        	$validator = $this->itemValidator($items);
            if ($validator->passes()) {
            	DB::beginTransaction();
	            foreach ($items as $i)
	            {
                    $this->insertItem($conferenceId, $i);
                }
            }
            else
                return response()->json(['message' => 'validation_failed', 'errors' => $validator->errors()], 422);
            DB::commit();
            return response()->json(['message' => 'items_added']);
        } catch (ValidationException $e) {
            DB::rollback();
            return response()->json(['message' => 'items_could_not_be_added', 'errors' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'unknown_error', 'errors' => $e->getMessage()], 500);
        }
    }

    /*
     * GET /api/conferences/{conferenceId}/inventory/reserve
     * POST /api/conferences/{conferenceId}/inventory/reserve
     * @param [{"id":"1","quantity":"1"},{"id":"2","quantity":"2"}]
     * - reserves an item for a conference
     */
    public function reserveItem($conferenceId, Request $req)
    {
        
    }

    /*
     * PATCH /api/conferences/{conferenceId}/inventory/{itemId}
     * - edit an item
     */
    public function editItem($conferenceId, $itemId, Request $req)
    {
        
    }

    /*
     * DELETE /api/conferences/{conferenceId}/inventory/{itemId}
     * - deletes an item
     */
    public function deleteItem($conferenceId, $itemId)
    {
        
    }
}
