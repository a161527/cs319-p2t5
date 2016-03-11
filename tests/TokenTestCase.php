<?php

const TEST_LOGIN = [
    'email' => 'root@localhost', 'password' => 'admin'
];

const NO_PERMISSION_LOGIN = [
    'email' => 'unprivileged@localhost', 'password' => 'secret'
];

/**
 * Handles test cases that need to pass tokens through to JWTAuth.
 *
 * This essentually results in grabbing a token before each test and
 * adding the appropriate header to every request after.
 *
 * Note on usage - set the protected field $noTokenNextReq to true if you
 * want to make a request without a token (to check that authentication
 * blocks things, for example.
 */
trait TokenTestCase {
    protected $token;
    protected $noTokenNextReq = false;

    protected function disablePrivileges() {
        $this->authWithLoginCredentials(NO_PERMISSION_LOGIN);
    }

    protected function enablePrivileges() {
        $this->authWithLoginCredentials(TEST_LOGIN);
    }

    protected function authWithLoginCredentials($credentials, $refresh = true) {
        parent::json('POST', '/api/login', $credentials);
        $this->token = json_decode($this->response->getContent())->token;
        if ($refresh) {
            $this->refreshApplication();
        }
    }

    public function setUp() {
        parent::setUp();
        $this->authWithLoginCredentials(TEST_LOGIN, false);
   }

    /*
     * Overrides the default call method for Laravel's TestCase (from CrawlerTrait)
     * and sets the auth header before continuing
     */
    public function call($method, $uri, $params = array(), $cookies = array(),
            $files = array(), $headers = array(), $content = null) {
        if(!$this->noTokenNextReq) {
            $headers['HTTP_Authorization'] = 'Bearer ' . $this->token;
        } else {
            $this->refreshApplication();
            $this->noTokenNextReq = false;
        }

        parent::call($method, $uri, $params, $cookies, $files, $headers, $content);
    }
}
