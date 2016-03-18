<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RoomManualSetupTest extends TestCase
{
    use \TokenTestCase;
    use DatabaseTransactions;

    private function assertGetResponseID() {
        $this->assertResponseOK();
        $decoded = json_decode($this->response->getContent());
        $this->assertTrue(
            isset($decoded->id),
            "ID field not set");
        return $decoded->id;
    }

    public function testCanGetSeededResidences(){
        $this->get('/api/conferences/1/residences')
             ->seeJson(
                 ["name" => "Foobar Res",
                  "location" => "Integer Drive"])
             ->seeJson(
                 ["name" => "Barbaz Res",
                  "location" => "XYZ Ave"]);
    }

    public function testCreateResidenceAndGet() {
        $data =
            ["name" => "TestRes",
             "location" => "Test Ave"];
        $this->json(
            'POST',
            '/api/conferences/1/residences',
            $data);

        $id = $this->assertGetResponseID();

        $this->get("/api/conferences/1/residences")
             ->seeJson($data);
    }

    public function testCreateRoomSetAndGet() {
        $data = [
            "name" => "102A",
            "type" => [
                "capacity" => 12,
                "accessible" => 0,
                "name" => "Type-12-no"
            ]
        ];

        $this->json('POST', '/api/conferences/1/residences/1/rooms', $data);
        $id = $this->assertGetResponseID();
        $data['type']['id'] = json_decode($this->response->getContent())->typeID;

        $this->get("/api/conferences/1/residences/1/rooms")
             ->seeJson($data);


        $data = [
            "rangeStart" => 20,
            "rangeEnd" => 29,
            "typeID" => 3,
        ];

        $this->json('POST', '/api/conferences/1/residences/2/rooms', $data);
        $id = $this->assertGetResponseID();
        unset ($data["typeID"]);
        $data["type"] = ["id" => 3, "name" => "TypeA-R2", "accessible"=>1,"capacity"=>4];

        $this->get("/api/conferences/1/residences/2/rooms")
             ->seeJson($data);
    }

    public function testGetSeededRoomTypes() {
        $this->get("/api/conferences/1/residences/2/roomTypes");
        $this->seeJson(
                 ["capacity" => 4,
                  "accessible" => 1]);
    }

    public function testCantListResidencesWithoutPermission() {
        $this->authWithLoginCredentials(NO_PERMISSION_LOGIN);
        $this->get("/api/conferences/1/residences")
             ->assertResponseStatus(403);
    }
}
