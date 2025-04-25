<?php

namespace Tests\SaboCore\Expose;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use SaboCore\Core\Mappers\Implementation\ArrayDtoMapper;
use Tests\SaboCore\Core\Global\ApplicationTest;

/**
 * SaboCore\Expose\utils.php test
 */
class UtilsFunctionsExposeTest extends ApplicationTest
{
    /**
     * @return string[][] Utils functions list
     */
    public static function getUtilsFunctionsList():array{
        return [
            ["arrayDtoMapper"]
        ];
    }

    #[TestDox(text: "Test utils function exist and singleton validity of (\$functionName")]
    #[DataProvider(methodName: "getUtilsFunctionsList")]
    public function testFunctionExists(string $functionName):void{
        $this->assertTrue(
            condition: function_exists(function: $functionName),
            message: "utils $functionName does not exist"
        );

        $this->assertSame(
            expected: call_user_func(callback: $functionName),
            actual: call_user_func(callback: $functionName),
            message: "The returned manager instance is not the same"
        );
    }
}