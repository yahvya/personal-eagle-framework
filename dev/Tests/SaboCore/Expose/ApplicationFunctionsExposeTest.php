<?php

namespace Tests\SaboCore\Expose;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\SaboCore\Core\Global\ApplicationTest;

/**
 * SaboCore\Expose\application.php test
 */
class ApplicationFunctionsExposeTest extends ApplicationTest
{
    /**
     * @return string[][] Application utils functions list
     */
    public static function getApplicationUtilsFunctionsList():array{
        return [
            ["application"],
            ["framework"],
        ];
    }

    #[TestDox(text: "Test application utils function exist and singleton validity of (\$functionName)")]
    #[DataProvider(methodName: "getApplicationUtilsFunctionsList")]
    public function testFunctionExists(string $functionName):void{
        $this->assertTrue(
            condition: function_exists(function: $functionName),
            message: "application utils $functionName does not exist"
        );

        $this->assertSame(
            expected: call_user_func(callback: $functionName),
            actual: call_user_func(callback: $functionName),
            message: "The returned manager instance is not the same"
        );
    }
}