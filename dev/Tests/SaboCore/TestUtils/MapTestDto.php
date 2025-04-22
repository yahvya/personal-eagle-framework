<?php

namespace Tests\SaboCore\TestUtils;

use SaboCore\Core\Mappers\Annotations\DtoMap;

/**
 * Test get mapping dto
 */
readonly class MapTestDto
{
    public string $name;

    #[DtoMap(alias: "version")]
    public string $testVersion;
}