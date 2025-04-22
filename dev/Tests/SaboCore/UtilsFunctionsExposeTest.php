<?php

namespace Tests\SaboCore;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
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

    #[TestDox(text: "Test utils function exist (\$functionName")]
    #[DataProvider(methodName: "getUtilsFunctionsList")]
    public function testFunctionExists(string $functionName):void{
        $this->assertTrue(
            condition: function_exists(function: $functionName),
            message: "utils $functionName does not exist"
        );
    }

    #[TestDox(text: "Test array dto mapper function")]
    public function testUtilsArrayDtoMapperFunction():void{
        $this->assertEquals(
            expected: arrayDtoMapper(),
            actual: arrayDtoMapper(),
            message: "The returned array dto manager instance is not the same"
        );
    }
}