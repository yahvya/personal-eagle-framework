<?php

namespace Tests\SaboCore;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\SaboCore\Core\Global\ApplicationTest;

/**
 * SaboCore\Expose\routing.php test
 */
class RoutingFunctionsExposeTest extends ApplicationTest
{
    /**
     * @return string[][] Routing functions list
     */
    public static function getRoutingFunctionsList():array{
        return [
            ["request"]
        ];
    }

    #[TestDox(text: "Test routing function exist (\$functionName")]
    #[DataProvider(methodName: "getRoutingFunctionsList")]
    public function testFunctionExists(string $functionName):void{
        $this->assertTrue(
            condition: function_exists(function: $functionName),
            message: "routing $functionName does not exist"
        );
    }

    #[TestDox(text: "Test routing function")]
    public function testRoutingRequestFunction():void{
        $this->assertEquals(
            expected: request(),
            actual: request(),
            message: "The returned request manager instance is not the same"
        );
    }
}