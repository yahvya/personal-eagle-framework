<?php

namespace Tests\SaboCore\Core\Mappers\Implementation;

use PHPUnit\Framework\Attributes\TestDox;
use SaboCore\Core\Mappers\Implementation\ArrayDtoMapper;
use Tests\SaboCore\TestUtils\MapTestDto;
use Tests\TestUtils\ApplicationTestCase;

/**
 * SaboCore\Core\Mappers\Implementation\ArrayDtoMapper test
 */
class ArrayDtoMapperTest extends ApplicationTestCase
{
    #[TestDox(text: "Test map method")]
    public function testMap():void{
        $arrayToTest = [
            "name" => "Test name",
            "version" => "version 1.0.0"
        ];

        $resultDto = new ArrayDtoMapper()->map(data: $arrayToTest,in: MapTestDto::class);

        $this->assertInstanceOf(
            expected: MapTestDto::class,
            actual: $resultDto,
            message: "Invalid returned dto"
        );

        $this->assertEquals(
            expected: $arrayToTest["name"],
            actual: $resultDto->name,
            message: "Invalid name value"
        );

        $this->assertEquals(
            expected: $arrayToTest["version"],
            actual: $resultDto->testVersion,
            message: "Invalid version value"
        );
    }
}