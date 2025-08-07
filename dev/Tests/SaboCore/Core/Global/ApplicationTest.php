<?php

namespace Tests\SaboCore\Core\Global;

use PHPUnit\Framework\Attributes\TestDox;
use SaboCore\Core\Global\Application;
use Tests\TestUtils\ApplicationTestCase;

/**
 * SaboCore\Core\Global\Application class test
 */
class ApplicationTest extends ApplicationTestCase
{
    #[TestDox(text: "Test application init method which should load initial functions")]
    public function testInitApplication():void
    {
        new Application()->init();

        $this->assertTrue(
            condition: function_exists(function: "request"),
            message: "An initial function does not exist"
        );
    }
}