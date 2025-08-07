<?php

namespace SaboCore\Core\Global;

use SaboCore\Core\Scanners\DirectoryFunctionScanner;

/**
 * Application manager
 */
class Application{
    /**
     * @return $this Init the application requirements
     */
    public function init():static
    {
        $dirScanner = new DirectoryFunctionScanner();

        $exposeFunctionsPath = APPLICATION_ROOT . "/SaboCore/Expose";
        $functionsToExposeFilesPath = $dirScanner->scan(toScan: $exposeFunctionsPath);

        foreach($functionsToExposeFilesPath as $functionFilePath)
            require_once $functionFilePath;

        $this->importUserConfigurations();

        return $this;
    }

    /**
     * Import user configurations
     * @return void
     */
    protected function importUserConfigurations():void
    {
        $userConfigurationFiles = [
            "application.php",
            "framework.php"
        ];

        foreach($userConfigurationFiles as $filename)
            require_once APPLICATION_ROOT . "/Src/Configs/$filename";
    }
}