<?php

namespace Sabo\Application\Context;

/**
 * Application path configuration data contract
 */
readonly class PathConfigurationDto
{
    public function __construct(
        public string $rootDirectoryPath,
        public string $configurationsDirectoryPath
    ){}
}