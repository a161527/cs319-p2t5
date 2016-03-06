<?php

require ('TokenTestCase.php');

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ConferenceRegistrationTest extends TestCase
{
    use \TokenTestCase;
    const TEST_CONF_ID = 1;
    const TEST_ATTENDEE_ID = 1;

    const SIMPLE_REGISTRY_DATA = [
        "flight" => [
            "number" => 1,
            "airline" => "Laravair",
            "arrivalDate" => "2016-02-02",
            "arrivalTime" => "12:00",
            "airport" => "FOO",
        ],
        "attendees" => [self::TEST_ATTENDEE_ID],
        "needsTransportation" => false];
    //Fails - need to confirm the registration first, otherwise the flight is still
    //tentative
    public function testRegisterRejectsDifferentFlightTime() {
        $this->json("POST", "/api/conferences/1/register", self::SIMPLE_REGISTRY_DATA);
        $json = json_decode($this->response->getContent());
        $this->assertResponseOK();

        $id = $json->ids[0];
        $this->post("/api/conferences/1/register/${id}/approve");
        echo $this->response->getContent();
        $this->assertResponseOK();

        $changedTime = self::SIMPLE_REGISTRY_DATA;
        $changedTime["arrivalTime"] = "01:00";

        $this->json("POST", "/api/conferences/1/register", $changedTime);
        $this->assertResponseStatus(400);
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

        $this->json('POST', "/api/conferences/1/register");
        $this->assertResponseStatus(400);
    }

}
