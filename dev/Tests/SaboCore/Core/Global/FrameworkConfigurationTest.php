<?php

namespace Tests\SaboCore\Core\Global;

use PHPUnit\Framework\Attributes\TestDox;
use SaboCore\Core\Global\ApplicationConfiguration;
use Tests\TestUtils\ApplicationTestCase;

/**
 * SaboCore\Global\FrameworkConfiguration test
 */
class FrameworkConfigurationTest extends ApplicationTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        date_default_timezone_set(timezoneId: "America/New_York");
    }

    #[TestDox(text: "Test the application configuration timezone set method")]
    public function testSetTimezone():void{
        $applicationConfiguration = new ApplicationConfiguration();


        $applicationConfiguration->setTimezone(timezone: "Europe/Paris",persist: false);

        $this->assertTrue(
            condition: date_default_timezone_get() !== "Europe/Paris",
            message: "The timezone have been persist while expecting not"
        );

        $applicationConfiguration->setTimezone(timezone: "Europe/Paris");
        $this->assertTrue(
            condition: date_default_timezone_get() === "Europe/Paris",
            message: "The timezone have not been persist"
        );
    }
}