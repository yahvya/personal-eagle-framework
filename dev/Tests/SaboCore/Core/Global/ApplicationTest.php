<?php

namespace Tests\SaboCore\Core\Global;

use SaboCore\Core\Global\Application;
use Tests\SaboCore\Utils\ApplicationTestCase;

/**
 * SaboCore\Core\Global\Application class test
 */
class ApplicationTest extends ApplicationTestCase
{
    public function testInitApplication():void
    {
        new Application()->init();

        $this->assertTrue(
            condition: function_exists(function: "request"),
            message: "An initial function does not exist"
        );
    }
}