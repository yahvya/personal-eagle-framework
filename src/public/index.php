<?php

use Sabo\Application\Context\ApplicationContext;
use Sabo\Application\Context\PathConfigurationDto;

# define application default context
ApplicationContext::$current = new ApplicationContext(
    applicationPathConfiguration: new PathConfigurationDto(
        rootDirectoryPath: __DIR__ . "../..",
        configurationsDirectoryPath: "configs"
    )
);  