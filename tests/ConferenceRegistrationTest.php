<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ConferenceRegistrationTest extends TestCase
{
    const TEST_CONF_ID = 0;
    const TEST_ATTENDEE_ID = 0;

    const SIMPLE_REGISTRY_DATA = [
        "flight" => [
            "number" => 1,
            "airline" => "Laravair",
            "arrivalDate" => "2016-02-02",
            "arrivalTime" => "12:00:00",
            "airport" => "FOO"
        ],
        "attendees" => [self::TEST_ATTENDEE_ID]];

    public function testRegisterRejectsDifferentFlightTime() {
        $this->json("POST", "/api/conference/", self::SIMPLE_REGISTRY_DATA);
        $this->assertResponseOK();

        $changedTime = self::SIMPLE_REGISTRY_DATA;
        $changedTime["arrivalTime"] = "00:00:00";

        $this->json("POST", "/api/conference/", $changedTime);
        $this->assertResponseStatus(400);
    }
}
