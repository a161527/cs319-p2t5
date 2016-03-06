<?php

const TEST_LOGIN = [
    'email' => 'ryanchenkie@gmail.com', 'password' => 'secret'
];
trait TokenTestCase {
    protected $token;

    public function setUp() {
        parent::setUp();

        parent::json('POST', '/api/login', TEST_LOGIN);
        $this->token = json_decode($this->response->getContent())->token;
    }

    public function json($method, $uri, array $params = array(), array $headers = array()) {
        $headers['HTTP_Authorization'] = 'Bearer ' . $this->token;
        parent::json($method, $uri, $params, $headers);
    }
}
