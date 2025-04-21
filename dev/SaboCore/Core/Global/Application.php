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
        $exposeFunctionsPath = APPLICATION_ROOT . "/SaboCore/Expose";
        $functionsToExposeFilesPath = new DirectoryFunctionScanner()->scan(toScan: $exposeFunctionsPath);

        foreach($functionsToExposeFilesPath as $functionFilePath)
            require_once $functionFilePath;

        return $this;
    }
}