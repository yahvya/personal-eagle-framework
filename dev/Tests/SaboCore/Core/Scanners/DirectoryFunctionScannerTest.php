<?php

namespace Tests\SaboCore\Core\Scanners;

use PHPUnit\Framework\Attributes\TestDox;
use SaboCore\Core\Scanners\DirectoryFunctionScanner;
use Tests\TestUtils\ApplicationTestCase;

/**
 * SaboCore\Core\Scanners\DirectoryFunctionScanner test
 */
class DirectoryFunctionScannerTest extends ApplicationTestCase
{
    #[TestDox(text: "Test scan method")]
    public function testScan():void{
        $functionsDirectoryPath = __DIR__ . "/../../TestUtils/functions-directory";

        $result = new DirectoryFunctionScanner()->scan(toScan: $functionsDirectoryPath);
        $expectedResult = [
            "$functionsDirectoryPath/one.php",
            "$functionsDirectoryPath/two.php"
        ];

        $this->assertArrayIsEqualToArrayIgnoringListOfKeys(
            expected: $expectedResult,
            actual: $result,
            keysToBeIgnored: []
        );
    }
}