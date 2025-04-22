<?php

namespace Tests\SaboCore\Core\Http;

use PHPUnit\Framework\Attributes\TestDox;
use SaboCore\Core\Http\RequestManager;
use Tests\SaboCore\TestUtils\MapTestDto;
use Tests\TestUtils\ApplicationTestCase;

/**
 * SaboCore\Core\Http\RequestManager test
 */
class RequestManagerTest extends ApplicationTestCase
{
    public static function setUpBeforeClass(): void
    {
        $_GET = [
            "name" => "get Sabo framework",
            "version" => "get 1.0.0"
        ];

        $_POST = [
            "name" => "post Sabo framework",
            "version" => "post 1.0.0"
        ];

        parent::setUpBeforeClass();
    }

    #[TestDox(text: "Test request manager params method")]
    public function testParams():void
    {
        $this->assertArrayIsEqualToArrayIgnoringListOfKeys(
            expected: new RequestManager()->params(),
            actual: $_GET,
            keysToBeIgnored: [],
            message: "The return request params does not match the get variables"
        );
    }

    #[TestDox(text: "Test request manager param method")]
    public function testParam():void{
        $this->assertEquals(
            expected: $_GET["name"],
            actual: new RequestManager()->param(getName: "name"),
            message: "The returned param value is not the same as the get value"
        );
    }

    #[TestDox(text: "Test request manager map params in method")]
    public function testMapParamsIn():void{
        $resultDto = new RequestManager()->mapParamsIn(dto: MapTestDto::class);

        $this->assertInstanceOf(
            expected: MapTestDto::class,
            actual: $resultDto,
            message: "The returned data contract is the expected type"
        );

        $this->assertEquals(
            expected: $_GET["name"],
            actual: $resultDto->name,
            message: "Provided name is not the same"
        );

        $this->assertEquals(
            expected: $_GET["version"],
            actual: $resultDto->testVersion,
            message: "Provided version is not the same"
        );
    }

    #[TestDox(text: "Test request manager post values method")]
    public function testPostValues():void{
        $this->assertArrayIsEqualToArrayIgnoringListOfKeys(
            expected: $_POST,
            actual: new RequestManager()->postValues(),
            keysToBeIgnored: []
        );
    }

    #[TestDox(text: "Test request manager post value method")]
    public function testPostValue():void{
        $this->assertEquals(
            expected: $_POST["name"],
            actual: new RequestManager()->postValue(postName: "name"),
            message: "The returned post value is not the same as the post value"
        );
    }

    #[TestDox(text: "Test request manager map post values in method")]
    public function testMapPostValuesIn():void{
        $resultDto = new RequestManager()->mapPostValuesIn(dto: MapTestDto::class);

        $this->assertInstanceOf(
            expected: MapTestDto::class,
            actual: $resultDto,
            message: "The returned data contract is the expected type"
        );

        $this->assertEquals(
            expected: $_POST["name"],
            actual: $resultDto->name,
            message: "Provided name is not the same"
        );

        $this->assertEquals(
            expected: $_POST["version"],
            actual: $resultDto->testVersion,
            message: "Provided version is not the same"
        );
    }
}