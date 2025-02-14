<?php

namespace Sabo\Application\Context\DataContracts;

/**
 * Application path configuration data contract
 */
readonly class PathConfigurationDto
{
    /**
     * @param string $rootDirectoryPath Application root directory
     * @param string $configurationsDirectoryPath Configurations directory path from Src directory
     */
    public function __construct(
        public string $rootDirectoryPath,
        public string $configurationsDirectoryPath
    ){}
}