<?php

namespace Tests\SaboCore\Expose;

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

    #[TestDox(text: "Test routing function exist and singleton validity of(\$functionName")]
    #[DataProvider(methodName: "getRoutingFunctionsList")]
    public function testFunctionExists(string $functionName):void{
        $this->assertTrue(
            condition: function_exists(function: $functionName),
            message: "routing $functionName does not exist"
        );

        $this->assertSame(
            expected: call_user_func(callback: $functionName),
            actual: call_user_func(callback: $functionName),
            message: "The returned manager instance is not the same"
        );
    }
}