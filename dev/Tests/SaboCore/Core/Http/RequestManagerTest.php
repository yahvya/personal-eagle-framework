<?php

namespace Tests\SaboCore\Core\Http;

use PHPUnit\Framework\Attributes\TestDox;
use SaboCore\Core\Http\RequestManager;
use Tests\SaboCore\TestUtils\MapTestDto;
use Tests\SaboCore\TestUtils\MockPhpInput;
use Tests\TestUtils\ApplicationTestCase;

/**
 * SaboCore\Core\Http\RequestManager test
 */
class RequestManagerTest extends ApplicationTestCase
{
    public static function setUpBeforeClass(): void
    {
        $_GET = [
            'name' => 'get Sabo framework',
            'version' => 'get 1.0.0'
        ];

        $_POST = [
            'name' => 'post Sabo framework',
            'version' => 'post 1.0.0'
        ];

        parent::setUpBeforeClass();
    }

    #[TestDox(text: 'Test request manager params method')]
    public function testParams(): void
    {
        $this->assertArrayIsEqualToArrayIgnoringListOfKeys(
            expected: new RequestManager()->params(),
            actual: $_GET,
            keysToBeIgnored: [],
            message: 'The return request params does not match the get variables'
        );
    }

    #[TestDox(text: 'Test request manager param method')]
    public function testParam(): void
    {
        $this->assertEquals(
            expected: $_GET['name'],
            actual: new RequestManager()->param(getName: 'name'),
            message: 'The returned param value is not the same as the get value'
        );
    }

    #[TestDox(text: 'Test request manager map params in method')]
    public function testMapParamsIn(): void
    {
        $resultDto = new RequestManager()->mapParamsIn(dto: MapTestDto::class);

        $this->assertInstanceOf(
            expected: MapTestDto::class,
            actual:  $resultDto,
            message: 'Bad instance'
        );
        $this->assertEquals($_GET['name'], $resultDto->name);
        $this->assertEquals($_GET['version'], $resultDto->testVersion);
    }

    #[TestDox(text: 'Test request manager post values method')]
    public function testPostValues(): void
    {
        $this->assertArrayIsEqualToArrayIgnoringListOfKeys(
            expected: $_POST,
            actual: new RequestManager()->postValues(),
            keysToBeIgnored: []
        );
    }

    #[TestDox(text: 'Test request manager post value method')]
    public function testPostValue(): void
    {
        $this->assertEquals(
            expected: $_POST['name'],
            actual: new RequestManager()->postValue(postName: 'name'),
            message: 'The returned post value is not the same as the post value'
        );
    }

    #[TestDox(text: 'Test request manager map post values in method')]
    public function testMapPostValuesIn(): void
    {
        $resultDto = new RequestManager()->mapPostValuesIn(dto: MapTestDto::class);

        $this->assertInstanceOf(
            expected: MapTestDto::class,
            actual: $resultDto,
            message: 'Bad instance'
        );
        $this->assertEquals($_POST['name'], $resultDto->name);
        $this->assertEquals($_POST['version'], $resultDto->testVersion);
    }

    #[TestDox(text: 'Test request manager input and mapping for PUT with JSON')]
    public function testInputAndMapInput(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $inputData = [
            'name' => 'input Sabo framework',
            'version' => 'input 2.0.0'
        ];

        $json = json_encode(value: $inputData);
        stream_wrapper_unregister(protocol: 'php');
        stream_wrapper_register(protocol: 'php',class: MockPhpInput::class);
        MockPhpInput::$body = $json;

        $manager = new RequestManager();

        $this->assertEquals($inputData, @$manager->input());

        $dto = @$manager->mapInputIn(dto: MapTestDto::class);

        $this->assertInstanceOf(
            expected: MapTestDto::class,
            actual: $dto,
            message: 'Bad instance'
        );
        $this->assertEquals(
            expected: $inputData['name'],
            actual: $dto->name,
            message: 'Names are not the same'
        );
        $this->assertEquals(
            expected: $inputData['version'],
            actual: $dto->testVersion,
            message: 'Versions are not the same'
        );

        stream_wrapper_restore(protocol: 'php');
    }

    #[TestDox(text: 'Test request manager map data in according to method')]
    public function testMapDataInForDelete(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $inputData = [
            'name' => 'delete Sabo framework',
            'version' => 'delete 2.0.0'
        ];

        $json = json_encode(value: $inputData);
        stream_wrapper_unregister(protocol: 'php');
        stream_wrapper_register(protocol: 'php',class: MockPhpInput::class);
        MockPhpInput::$body = $json;

        $dto = @new RequestManager()->mapDataIn(dto: MapTestDto::class);
        $this->assertInstanceOf(
            expected: MapTestDto::class,
            actual: $dto,
            message: 'Bad instance'
        );
        $this->assertEquals(
            expected: $inputData['name'],
            actual: $dto->name,
            message: 'Names are not the same'
        );
        $this->assertEquals(
            expected: $inputData['version'],
            actual: $dto->testVersion,
            message: 'Versions are not the same'
        );

        stream_wrapper_restore(protocol: 'php');
    }
}
