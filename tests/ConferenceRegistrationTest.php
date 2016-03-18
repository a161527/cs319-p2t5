<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

class ConferenceRegistrationTest extends TestCase
{
    use \TokenTestCase;

    const TEST_CONF_ID = 1;
    const TEST_ATTENDEE_ID = 1;

    const FLIGHT_DATA = [
        "number" => 1,
        "airline" => "Laravair",
        "arrivalDate" => "2016-02-02",
        "arrivalTime" => "12:00:00",
        "airport" => "FOO",
    ];

    const SIMPLE_REGISTRY_DATA = [[
        "flight" => self::FLIGHT_DATA,
        "attendees" => [self::TEST_ATTENDEE_ID],
        "needsTransportation" => 0,
        "needsAccommodation" => 0
    ]];

    public function testRegisterRejectsDifferentFlightTime() {
        $this->json("POST", "/api/conferences/1/register", self::SIMPLE_REGISTRY_DATA);
        $json = json_decode($this->response->getContent());
        $this->assertResponseOK();

        $id = $json[0]->ids[0];
        $this->post("/api/conferences/1/register/${id}/approve");
        $this->assertResponseOK();

        $changedTime = self::SIMPLE_REGISTRY_DATA;
        $changedTime[0]["flight"]["arrivalTime"] = "01:00:00";

        $this->json("POST", "/api/conferences/1/register", $changedTime);
        $this->assertResponseStatus(400);
    }

    public function testSameFlightDataWorks() {
        $this->json("POST", "/api/conferences/1/register", self::SIMPLE_REGISTRY_DATA);
        $this->assertResponseOK();

        $this->json("POST", "/api/conferences/1/register", self::SIMPLE_REGISTRY_DATA);
        $this->assertResponseOK();
    }

    public function testRejectsNonexplicitEmptyFlight() {
        $noflight = self::SIMPLE_REGISTRY_DATA;
        $noflight[0]['flight'] = null;

        $this->json('POST', "/api/conferences/1/register", $noflight);
        $this->assertResponseStatus(400);
    }

    public function testRejectsNegativeAttendeeID() {
        $negid = self::SIMPLE_REGISTRY_DATA;
        $negid['attendees'] = [-1,-2];

        $this->json('POST', "/api/conferences/1/register", $negid);
        $this->assertResponseStatus(400);
    }

    public function testBasicDataRetrieval() {
        $this->json('POST', "/api/conferences/1/register", self::SIMPLE_REGISTRY_DATA);
        $this->assertResponseOK();
        $id = json_decode($this->response->getContent())[0]->ids[0];

        $this->get("/api/conferences/1/register/${id}");
        $this->seeJson(self::FLIGHT_DATA);

        $this->noTokenNextReq = true;
        $this->get("/api/conferences/1/register/${id}");
        $this->assertResponseStatus(400);
    }

    public function testApprovalTriggersAggregation() {
        $this->expectsJobs(App\Jobs\RegistrationFlightAggregator::class);

        $this->json('POST', '/api/conferences/1/register', self::SIMPLE_REGISTRY_DATA);
        $this->assertResponseOK();
        $id = json_decode($this->response->getContent())[0]->ids[0];

        $this->post("/api/conferences/1/register/${id}/approve");
    }

    public function testApprovalDeniedWithoutPermission() {
        $this->authWithLoginCredentials(NO_PERMISSION_LOGIN);
        $data = self::SIMPLE_REGISTRY_DATA;

        //First user/dependent not owned by the first account
        $data[0]['attendees'] = [5];
        $this->json('POST', '/api/conferences/1/register', $data);
        $this->assertResponseOK();
        $id = json_decode($this->response->getContent())[0]->ids[0];

        $this->post("/api/conferences/1/register/${id}/approve");
        $this->assertResponseStatus(403);
    }

    public function testFailureIfDependentUnapproved() {
        $this->authWithLoginCredentials(NO_PERMISSION_LOGIN);
        $data = self::SIMPLE_REGISTRY_DATA;

        //Unapproved dependent
        $data['attendees'] = [6];
        $this->json('POST', '/api/conferences/1/register', $data);
        $this->assertResponseStatus(400);
    }

    public function testCannotRegisterUnownedDependent() {
        $this->authWithLoginCredentials(NO_PERMISSION_LOGIN);
        $data = self::SIMPLE_REGISTRY_DATA;

        $data['attendees'] = [1];
        $this->json('POST', '/api/conferences/1/register', $data);
        $this->assertResponseStatus(400);
    }
}
