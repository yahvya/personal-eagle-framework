<?php

namespace Tests\SaboCore\Utils;

use SaboCore\Core\Mappers\Annotations\DtoMap;

/**
 * Test get mapping dto
 */
readonly class RequestManagerUtilDto
{
    public string $name;

    #[DtoMap(alias: "version")]
    public string $testVersion;
}