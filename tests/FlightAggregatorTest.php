<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Jobs\RegistrationFlightAggregator;
use App\UserConference;
use App\Flight;

class FlightAggregatorTest extends TestCase
{
    use DatabaseTransactions;

    private $userNum = 1;

    protected function setUp() {
        parent::setUp();
        $userNum = 1;
    }

    private function createBasicFlight($number) {
        $flight = new Flight;
        $flight->flightNumber = $number;
        $flight->airline = "Foobar Air";
        $flight->arrivalDate = "2016-02-02";
        $flight->arrivalTime = "00:00:00";
        $flight->airport = "FOO";
        $flight->isChecked = true;

        $flight->save();
        return $flight;
    }

    private function createUserConferenceEntry($flightId) {
        $conference = new UserConference;
        $conference->userID = $this->userNum;
        $conference->flightID = $flightId;
        $conference->conferenceID = 1;
        $conference->needsTransportation = false;
        $conference->approved = true;

        $conference->save();

        $this->userNum += 1;
        return $conference;
    }

    private function runAggregator($registrationId) {
        $agg = new RegistrationFlightAggregator($registrationId);
        $agg->handle();
    }

    //Check that the aggregator actually does aggregate flights
    public function testAggregatesMatching() {
        $flight = $this->createBasicFlight(1000);
        $flightTwo = $this->createBasicFlight(1000);

        $conf = $this->createUserConferenceEntry($flight->id);
        $confTwo = $this->createUserConferenceEntry($flightTwo->id);

        $this->runAggregator($confTwo->id);
        $conf = UserConference::find($conf->id);

        $this->assertEquals($flightTwo->id, $conf->flight->id, "Flight not remapped");
        $this->assertNull(Flight::find($flight->id),  "Flight not deleted");
    }

    //Check that flights that don't match don't get aggregated
    public function testDoesNotAggregateOtherNumber() {
        $flight = $this->createBasicFlight(1000);
        $flightTwo = $this->createBasicFlight(1001);

        $conf = $this->createUserConferenceEntry($flight->id);
        $confTwo = $this->createUserConferenceEntry($flightTwo->id);

        $this->runAggregator($confTwo->id);

        $conf = UserConference::find($conf->id);

        $this->assertEquals($flight->id, $conf->flight->id,
            "Flight got changed: original " . $flight->id . " other " . $flightTwo->id);
    }

    public function testDoesNotAggregateDifferentDate() {
        $flight = $this->createBasicFlight(1000);
        $flight->arrivalDate = "2016-01-01";
        $flight->save();

        $flightTwo = $this->createBasicFlight(1000);
        $flightTwo->arrivalDate = "2016-01-02";
        $flightTwo->save();

        $conf = $this->createUserConferenceEntry($flight->id);
        $confTwo = $this->createUserConferenceEntry($flightTwo->id);

        $this->runAggregator($confTwo->id);

        $conf = UserConference::find($conf->id);

        $this->assertEquals($flight->id, $conf->flight->id,
            "Flight got changed: original " . $flight->id . " other " . $flightTwo->id);
    }

    //This shouldn't ever actually happen, and we should warn admins before
    //this case occurs, but if it does, we should avoid breaking it.
    //
    //(Technically can't have flights which are identical except for arrival time
    //as that would break the flight number uniqueness)
    public function testDoesNotAggregateDifferentTime() {
        $flight = $this->createBasicFlight(1000);
        $flight->arrivalTime = "12:00:00";
        $flight->save();

        $flightTwo = $this->createBasicFlight(1000);
        $flightTwo->arrivalTime = "12:00:01";
        $flightTwo->save();

        $conf = $this->createUserConferenceEntry($flight->id);
        $confTwo = $this->createUserConferenceEntry($flightTwo->id);

        $this->runAggregator($confTwo->id);

        $conf = UserConference::find($conf->id);

        $this->assertEquals($flight->id, $conf->flight->id,
            "Flight got changed: original " . $flight->id . " other " . $flightTwo->id);
    }
}
