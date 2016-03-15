<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ConferenceTest extends TestCase
{

    use \TokenTestCase;
    use DatabaseTransactions;

    const SIMPLE_CONF_CREATEDATA = [
        'name' => 'Foo',
        'start' => '2016-02-05',
        'end' => '2016-02-02',
        'location' => 'Earth',
        'hasTransportation' => 0,
        'hasAccommodations' => 0];

    private function createGetId(){
        $this->json('POST', '/api/conferences', self::SIMPLE_CONF_CREATEDATA);
        return json_decode($this->response->getContent())->id;
    }

    public function testCreateOK(){
        $this->json('POST', '/api/conferences', self::SIMPLE_CONF_CREATEDATA);
        $this->assertResponseOK();
    }

    public function testRejectsUnprivilegedCreate() {
        $this->disablePrivileges();
        $this->json('POST', '/api/conferences', self::SIMPLE_CONF_CREATEDATA);
        $this->assertResponseStatus(403);
    }

    public function testCreateReturnsDifferingIDs()
    {
        $startId = $this->createGetId();
        //Note: $response is a protected variable in a superclass
        $this->json('POST', '/api/conferences', self::SIMPLE_CONF_CREATEDATA)
            ->seeJsonEquals(['id' => $startId + 1]);
    }

    public function testCreateRetrieveGivesIdentical()
    {
        $id = $this->createGetId();
        $this->get("/api/conferences/{$id}")
            ->seeJson(self::SIMPLE_CONF_CREATEDATA);

        $this->noTokenNextReq = true;
        $this->get("/api/conferences/{$id}");
        $this->seeJson(self::SIMPLE_CONF_CREATEDATA);
    }

    public function testConferencesIncludedInFullList()
    {
        $this->json('POST', '/api/conferences', self::SIMPLE_CONF_CREATEDATA);
        $this->get("/api/conferences")
            ->seeJson(self::SIMPLE_CONF_CREATEDATA);

        $this->noTokenNextReq = true;
        $this->get("/api/conferences")
            ->seeJson(self::SIMPLE_CONF_CREATEDATA);
    }

    public function testConferenceListIncludesMultiple()
    {
        $firstId = $this->createGetID();
        $secondId = $this->createGetID();

        $this->get("/api/conferences")
            ->seeJson(["id" => $firstId])
            ->seeJson(["id" => $secondId]);
    }

    public function testDeleteRemovesConference()
    {
        $id = $this->createGetID();

        $this->delete("/api/conferences/{$id}");
        $this->assertResponseOK();
        $this->get("/api/conferences/{$id}");
        $this->assertResponseStatus(404);
    }

    public function testPutReplacesData()
    {
        $id = $this->createGetId();

        $newData = self::SIMPLE_CONF_CREATEDATA;
        $newData["hasTransportation"] = 1;

        $this->json("PUT", "/api/conferences/{$id}", $newData);
        $this->assertResponseOK();
        $this->get("/api/conferences/{$id}")->seeJson($newData);

    }
}
