<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Inventory;
use App\UserInventory;
use App\Conference;
use Validator;
use DB;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['authenticate', 'token']]);
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
            '*.totalQuantity' => 'required|numeric|min:0',
            // '*.units' => 'required|numeric|min:0',
            // allow alphanum itemName with dashes, brackets,
            '*.itemName' => 'required|regex:/^[a-zA-Z0-9()\s-]+$/',
            '*.disposable' => 'required|boolean'
        ]);

        return $validator;
    }

    /*
     * Get a validator for an incoming item array add request.
     */
    protected function reserveItemValidator(array $data)
    {
        $validator = Validator::make($data, [
            '*.id' => 'required|numeric|min:1',
            '*.quantity' => 'required|numeric|min:0'
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
        // $item->units = $data['units'];
        $item->itemName = $data['itemName'];
        $item->disposable = $data['disposable'];
        $item->conferenceID = $conferenceId;
        $item->save();
    }

    /* 
     * 
     * 
     */
    protected function editTotalQuantity(&$item, $newValue)
    {
    	// TODO: reduce total qty and current qty by the difference, after doing checks 
    	//       to make sure both do not go below zero
    }

    /*
     *
     *
     */
    protected function createUserInventoryEntry($dependentId, $inventoryId, $quantity, $conferenceId)
    {
        $entry = new UserInventory();
        $entry->userID = $dependentId;
        $entry->inventoryID = $inventoryId;
        $entry->unitCount = $quantity;
        $entry->conferenceID = $conferenceId;
        $entry->save();
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
     * GET api/conferences/{conferenceId}/inventory/unapproved
     * - return a list showing the inventory of a conference
     */
    public function unapproved($conferenceId)
    {
        // TODO: add permission/role filter
        $conf = Conference::where('id', '=', $conferenceId)->first();
        if ($conf === null)
            return response()->json(['message' => 'conference_not_found'], 404);
        else
        {
            $inventory = UserInventory::where('conferenceID', $conf->id)
                                    ->where('approved', 0)
                                    ->get();
            return response()->json(['message' => 'returned_unapproved_inventory', 'inventory' => $inventory]);
        }
    }

    /*
     * POST api/conferences/{conferenceId}/inventory
     * PUT api/conferences/{conferenceId}/inventory
     * - takes a list of JSON objects
     * - add a list of items to a conference's inventory
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
     * POST /api/conferences/{conferenceId}/inventory/reserve
     * @param [{"id":"1","quantity":"1"},{"id":"2","quantity":"2"}]
     * - reserves an item for a conference
     */
    public function reserveItem($conferenceId, Request $req)
    {
        // TODO: check that the amount reserved is <= the current qty
        $reservations = $req->all();
        try
        {
	        DB::beginTransaction();
	        $validator = $this->reserveItemValidator($reservations);
	        if ($validator->passes())
	        {
		        foreach ($reservations as $r)
		        {
		        	$item = Inventory::where('id', $r["id"])->where('conferenceID', $conferenceId)->first();
		        	if ($r["quantity"] <= $item->currentQuantity)
		        	{
		        		$item->currentQuantity -= $r["quantity"];
                        $item->save();
                        $this->createUserInventoryEntry($r["dependentID"], $r["id"], $r["quantity"], $conferenceId);
		        	}
		        	else
		        	{
		        		DB::rollback();
		        		return response()->json(['message' => 'out_of_stock', 'errors' => 'item id='+$r["id"]+'has less items remaining than requested'], 422);
		        	}
		        }
		    }
		    else
		    	return response()->json(['message' => 'validation_failed', 'errors' => $validator->errors()], 422);
		    DB::commit();
		    return response()->json(['message' => 'items_reserved']);
	    } catch (ValidationException $e) {
	    	return response()->json(['message' => 'validation_failed', 'errors' => $e->getMessage()], 422);
	    } catch (\Exception $e) {
	    	DB::rollback();
            return response()->json(['message' => 'unknown_error', 'errors' => $e->getMessage()], 500);
	    }
    }

    /*
     * PATCH /api/conferences/{conferenceId}/inventory/{itemId}
     * - edit an item
     */
    public function editItem($conferenceId, $itemId, Request $req)
    {
        $changes = $req->all();
        $item = Inventory::where('id', $itemId)
                    ->where('conferenceID', $conferenceId)
                    ->first();
        if (!$item)
            return response()->json(['message' => 'item_does_not_exist'], 422);
        else 
        {
            $validator = $this->itemEditValidator($changes);
            if ($validator->passes())
                // do updates
                foreach ($changes as $field => $newValue)
                {
                    switch($field)
                    {
                        // case "totalQuantity":
                        //     $this->editTotalQuantity($item, $newValue);
                        //     break;
                        case "itemName":
                            $item->itemName = $newValue;
                            break;
                        case "disposable":
                            $item->disposable = $newValue;
                            break;
                    }
                }
            else
                return response()->json(['message' => 'validation_failed', 'errors' => $validator->errors()], 422);
        }

        if ($item->save())
            return response()->json(['message' => 'item_updated'], 200);
        else
            return response()->json(['message' => 'item_could_not_be_updated'], 500);
    }

    /*
     * DELETE /api/conferences/{conferenceId}/inventory/{itemId}
     * - deletes an item
     */
    public function deleteItem($conferenceId, $itemId)
    {
		$item = Inventory::where('conferenceID', $conferenceId)
                    ->where('id', $itemId);
        if ($item->delete())
            return response()->json(['message' => 'item_deleted'], 200);
        else
            return response()->json(['message' => 'item_could_not_be_deleted'], 500);
    }

    /*
     * GET /api/userinventory/{id}/approve
     * POST /api/userinventory/{id}/approve
     * - approves an item request
     */
    public function approveRequest($userInventoryId)
    {
        $item = UserInventory::where('id', $userInventoryId)->first();
        $item->approved = 1;
        if ($item->save())
            return response()->json(['message' => 'item_approved'], 200);
        else
            return response()->json(['message' => 'item_could_not_be_approved'], 500);
    }
}
