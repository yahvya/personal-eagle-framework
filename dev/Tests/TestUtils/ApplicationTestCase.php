<?php

namespace Tests\TestUtils;

use PHPUnit\Framework\TestCase;

/**
 * A framework test class base
 */
abstract class ApplicationTestCase extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        require_once __DIR__ . "/../../public/index.php";

        $_SERVER["REQUEST_URI"] = "/test/link";
    }
}