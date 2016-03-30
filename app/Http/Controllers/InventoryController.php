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
use Entrust;
use App\Utility\PermissionNames;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth.rejection');
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

    protected function itemEditValidator($req) {
        return Validator::make($req,
            [
                'totalQuantity' => 'numeric|min:0',
                'itemName' => 'regex:/^[a-zA-Z0-9()\s-]+$/',
                'disposable' => 'boolean'
            ]);
    }

    /*
     * Get a validator for an incoming item array add request.
     */
    protected function reserveItemValidator(array $data)
    {
        $validator = Validator::make($data, [
            '*.id' => 'required|numeric|min:1',
            '*.quantity' => 'required|numeric|min:0',
            '*.dependentID' => 'required|numeric|min:0'
        ]);

        return $validator;
    }

    /*
     * Insert a validated user into the database
     */
    protected function insertItem($conferenceId, $data)
    {
        $item = new Inventory();
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
    protected function createUserInventoryEntry($dependentId, $inventoryId, $quantity, $conferenceId)
    {
        $entry = new UserInventory();
        $entry->userID = $dependentId;
        $entry->inventoryID = $inventoryId;
        $entry->unitCount = $quantity;
        $entry->conferenceID = $conferenceId;
        $entry->save();
    }

    private function displayItems($itemList) {
        $data = [];
        foreach ($itemList as $i) {
            $data[] = $this->displaySingleItem($i);
        }

        return $data;
    }

    private function displaySingleItem($item) {
        //Include calculated currentQuantity field
        $data = $item->toArray();
        $data['currentQuantity'] = $item->currentQuantity;
        return $data;
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
            $inventory = $this->displayItems($inventory);
            return response()->json(['message' => 'returned_inventory', 'inventory' => $inventory]);
        }
    }

    public function getItem($conferenceId, $itemId) {
        $invItem = Inventory::where('conferenceID', $conferenceId)->find($itemId);
        if (!isset($invItem)) {
            return response("",404);
        }
        return $this->displaySingleItem($invItem);
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
                                    ->with('user')
                                    ->with('inventory')
                                    ->get();
            return response()->json(['message' => 'returned_unapproved_inventory', 'inventory' => $inventory]);
        }
    }

    /* GET api/conferences/{conferenceId}/inventory/approved
     * - return a list showing the inventory of a conference
     */
    public function approved($conferenceId)
    {
        // TODO: add permission/role filter
        $conf = Conference::where('id', '=', $conferenceId)->first();
        if ($conf === null)
            return response()->json(['message' => 'conference_not_found'], 404);
        else
        {
            $inventory = UserInventory::where('conferenceID', $conf->id)
                                    ->where('approved', 1)
                                    ->with('user')
                                    ->with('inventory')
                                    ->get();
            return response()->json(['message' => 'returned_approved_inventory', 'inventory' => $inventory]);
        }
    }

    /*
     * POST api/conferences/{conferenceId}/inventory
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
                    if (!isset($item)) {
                        DB::rollback();
                        return response()->json(['message' => 'item_not_found', 'errors' => "item id={$r['id']} not found"], 404);
                    }
                    if ($r["quantity"] <= $item->currentQuantity)
                    {
                        $this->createUserInventoryEntry($r["dependentID"], $r["id"], $r["quantity"], $conferenceId);
                    }
                    else
                    {
                        DB::rollback();
                        return response()->json(['message' => 'out_of_stock', 'errors' => "item id={$r["id"]} has less items remaining than requested"], 422);
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
     * DELETE
     *  - delete an item reservation
     */
    public function deleteReservation($confId, $reservationId) {
        $reservation = UserInventory::with('inventory')->find($reservationId);
        if (is_null($reservation)) {
            return response()->json(["message" => "reservation_deleted"]);
        }

        if ($reservation->inventory->conferenceID != $confId) {
            return response()->json(["message" => "conference_request_mismatch"], 404);
        }

        if (!Entrust::can(PermissionNames::ConferenceInventoryEdit($confId))
                && $reservation->userID != Auth::user()->id) {
            return response()->json(["message" => "reservation_not_accessible"], 403);
        }

        $reservation->delete();
        return response()->json(["message" => "reservation_deleted"]);
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
                    //Need to cast because otherwise bad inputs get through and can cause weird results
                    switch((string)$field)
                    {
                        case "totalQuantity":
                            $item->totalQuantity = $newValue;
                            break;
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

        if ($item->currentQuantity < 0) {
            return response()->json(['message' => 'causes_bad_current_quantity'], 400);
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
    public function approveRequest($conferenceId, $userInventoryId)
    {
        if (!Entrust::can(PermissionNames::ConferenceInventoryEdit($conferenceId))) {
            return response()->json(['message' => 'inventory_list_edit_denied'], 403);
        }

        $item = UserInventory::with('inventory')->find($userInventoryId);
        if (!isset($item)) {
            return response()->json(['message' => 'request_not_found'],404);
        }
        if ($item->inventory->conferenceID != $conferenceId) {
            return response()->json(['message' => 'request_not_found_for_conference'], 404);
        }
        $item->approved = 1;
        if ($item->save())
            return response()->json(['message' => 'item_approved'], 200);
        else
            return response()->json(['message' => 'item_could_not_be_approved'], 500);
    }
}
