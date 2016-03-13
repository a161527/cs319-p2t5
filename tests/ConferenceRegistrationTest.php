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

    const SIMPLE_REGISTRY_DATA = [
        "flight" => self::FLIGHT_DATA,
        "attendees" => [self::TEST_ATTENDEE_ID],
        "needsTransportation" => false];

    public function testRegisterRejectsDifferentFlightTime() {
        $this->json("POST", "/api/conferences/1/register", self::SIMPLE_REGISTRY_DATA);
        $json = json_decode($this->response->getContent());
        $this->assertResponseOK();

        $id = $json->ids[0];
        $this->post("/api/conferences/1/register/${id}/approve");
        $this->assertResponseOK();

        $changedTime = self::SIMPLE_REGISTRY_DATA;
        $changedTime["flight"]["arrivalTime"] = "01:00:00";

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
        $noflight['flight'] = null;

        $this->json('POST', "/api/conferences/1/register");
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
        $id = json_decode($this->response->getContent())->ids[0];

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
        $id = json_decode($this->response->getContent())->ids[0];

        $this->post("/api/conferences/1/register/${id}/approve");
    }

    public function testApprovalDeniedWithoutPermission() {
        $this->authWithLoginCredentials(NO_PERMISSION_LOGIN);
        $data = self::SIMPLE_REGISTRY_DATA;

        //First user/dependent not owned by the first account
        $data['attendees'] = [5];
        $this->json('POST', '/api/conferences/1/register', $data);
        $this->assertResponseOK();
        $id = json_decode($this->response->getContent())->ids[0];

        $this->post("/api/conferences/1/register/${id}/approve");
        $this->assertResponseStatus(403);
    }
}
