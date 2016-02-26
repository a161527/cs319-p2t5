<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ConferenceTest extends TestCase
{

    const SIMPLE_CONF_CREATEDATA = [
        'name' => 'Foo',
        'start' => 'Jan 1',
        'end' => 'Jan 2',
        'location' => 'Earth',];

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreateReturnsDifferingIDs()
    {
        //Note: $response is a protected variable in a superclass
        $this->json('POST', '/api/conferences', self::SIMPLE_CONF_CREATEDATA);
        $startId = json_decode($this->response->getContent())->id;
        $this->json('POST', '/api/conferences', self::SIMPLE_CONF_CREATEDATA)
            ->seeJsonEquals(['id' => $startId + 1]);
    }
}
