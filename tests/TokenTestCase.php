<?php

const TEST_LOGIN = [
    'email' => 'ryanchenkie@gmail.com', 'password' => 'secret'
];
trait TokenTestCase {
    protected $token;
    protected $skipNextReq = false;

    public function setUp() {
        parent::setUp();

        parent::json('POST', '/api/login', TEST_LOGIN);
        $this->token = json_decode($this->response->getContent())->token;
    }

    public function json($method, $uri, array $params = array(), array $headers = array()) {
        if (!$this->skipNextReq) {
            $headers['HTTP_Authorization'] = 'Bearer ' . $this->token;
        } else {
	    $this->refreshApplication();
            $skipNextReq = false;
        }
        parent::json($method, $uri, $params, $headers);
    }

    public function call($method, $uri, $params = array(), $cookies = array(),
        $files = array(), $headers = array(), $content = null) {
        if(!$this->skipNextReq) {
            $headers['HTTP_Authorization'] = 'Bearer ' . $this->token;
        } else {
	    $this->refreshApplication();
            $skipNextReq = false;
        }
        parent::call($method, $uri, $params, $cookies, $files, $headers, $content);
    }
}
