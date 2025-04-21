<?php

namespace Tests\SaboCore\Core\Http;

use SaboCore\Core\Http\RequestManager;
use Tests\SaboCore\Utils\ApplicationTestCase;

/**
 * SaboCore\Core\Http\RequestManager test
 */
class RequestManagerTest extends ApplicationTestCase
{
    public static function setUpBeforeClass(): void
    {
        $_GET = [
            "name" => "Sabo framework",
            "version" => "1.0.0"
        ];

        parent::setUpBeforeClass();
    }

    public function testParams():void
    {
        $this->assertArrayIsEqualToArrayIgnoringListOfKeys(
            expected: new RequestManager()->params(),
            actual: $_GET,
            keysToBeIgnored: [],
            message: "The return request params does not match the get variables"
        );
    }
}