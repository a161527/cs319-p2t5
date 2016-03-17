<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Validator;

class InventoryController extends Controller
{
    public function __construct()
    {
    	// $this->middleware('jwt.auth', ['except' => ['authenticate', 'token']]);
        // provides an authorization header with each response
        // $this->middleware('jwt.refresh', ['except' => ['authenticate', 'token']]);
    }

    /*
     * GET api/conferences/{conferenceId}/inventory
     * - return a list showing the inventory of a conference
     */
    public function index($conferenceId)
    {

    }

	/*
	 * POST api/conferences/{conferenceId}/inventory
	 * PUT api/conferences/{conferenceId}/inventory
	 * - add an item to a conference's inventory
	 */
	public function addItem($conferenceId, Request $req)
	{

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
