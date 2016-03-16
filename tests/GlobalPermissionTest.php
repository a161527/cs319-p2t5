<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GlobalPermissionTest extends TestCase
{
    use \TokenTestCase;

    public function testHasAllGlobalPermissions() {
        $this->get("/api/permissions");
        $result = json_decode($this->response->getContent(), true);
        $this->assertArraySubset(
            ["create-conference", "manage-global-permissions", "approve-user-registration", "view-site-statistics"],
            $result);
    }
}
