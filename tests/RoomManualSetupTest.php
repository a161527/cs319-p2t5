<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RoomManualSetupTest extends TestCase
{
    use \TokenTestCase;
    use DatabaseTransactions;

    private function assertResponseIDExists() {
        $this->assertResponseOK();
        $this->assertTrue(isset(json_decode($this->response)->id), "ID field not set");
    }

    public function testCanGetSeededResidences(){
        $this->markTestIncomplete();
        $this->get('/api/conferences/1/residences')
             ->seeJson(
                 ["name" => "Foobar Res",
                  "location" => "Integer Drive"])
             ->seeJson(
                 ["name" => "Barbaz Res",
                  "location" => "XYZ Ave"]);
    }

    public function testCreateResidenceAndGet() {
        $this->markTestIncomplete();
        $data =
            ["name" => "TestRes",
             "location" => "Test Ave"];
        $this->json(
            'POST',
            '/api/conferences/1/residences',
            $data);

        $this->assertResponseIDExists();

        $id = json_decode($this->response)->id;
        $this->get("/api/conferences/1/residences")
             ->seeJson($data);
    }

    public function testCreateRoomSetAndGet() {
        $this->markTestIncomplete();
        $data = [
            "name" => "102A",
            "type" => [
                "capacity" => 12,
                "accessible" => false
            ]
        ];

        $this->json('POST', '/api/conferences/1/residences/1/rooms', $data);
        $this->assertResponseIDExists();

        $id = json_decode($this->response)->id;
        $this->get("/api/conferences/1/residences/1/rooms")
             ->seeJson($data);


        $data = [
            "rangeStart" => 20,
            "rangeEnd" => 29,
            "typeID" => 3
        ];

        $this->json('POST', '/api/conferences/1/residences/2/rooms', $data);
        $this->assertResponseIDExists();

        $id = json_decode($this->response)->id;
        $this->get("/api/conferences/1/residences/2/rooms")
             ->seeJson($data);
    }

    public function testGetSeededRoomTypes() {
        $this->markTestIncomplete();
        $this->get("/api/conferences/1/residences/2/roomTypes")
             ->seeJson(
                 ["capacity" => 4,
                  "accessible" => true]);
    }

    public function testCantListResidencesWithoutPermission() {
        $this->markTestIncomplete();
        $this->authWithLoginCredentials(NO_PERMISSION_LOGIN);
        $this->get("/api/conferences/1/residences")
             ->assertResponseStatus(403);
    }
}
