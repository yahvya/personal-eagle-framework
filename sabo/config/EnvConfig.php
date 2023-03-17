<?php

namespace Sabo\Config;

use Sabo\Helper\FileHelper;

/**
 * représente la configuration d'environnement
 */
abstract class EnvConfig{
    /**
     * configuration pouvant être envoyée aux vues
     */
    private static array $viewEnv = [];
    /**
     * configurations utilisés dans le programe
     */
    private static array $configEnv = [];

    private static bool $isJsonEnv;

    /**
     * lis le fichier environnement
     * @return bool si le fichier a été correctement lu
     */
    public static function readEnv():bool{
        $envFilePath = PathConfig::ENV_FILEPATH->value . SaboConfig::getStrConfig(SaboConfigAttributes::ENV_FILE_TYPE);

        $fileHelper = new FileHelper($envFilePath);

        $fileContent = $fileHelper->getFileContent();

        if($fileContent == null) return false;

        switch($fileHelper->getExtension() ){
            case ".json":
                list("viewEnv" => self::$viewEnv,"configEnv" => self::$configEnv) = $fileContent;

                self::$isJsonEnv = true;
            ; break;

            case ".env":
                $lower = SaboConfig::getStrConfig(SaboConfigAttributes::BASIC_ENV_FORVIEW_PREFIX);
                $upper = strtoupper($lower);

                self::$isJsonEnv = false;

                foreach($fileContent as $envKey => $value){
                    if(str_starts_with($envKey,$lower) )
                        self::$viewEnv[str_replace($lower,"",$envKey)] = $value;
                    elseif(str_starts_with($envKey,$upper) )
                        self::$viewEnv[str_replace($upper,"",$envKey)] = $value;
                    else
                        self::$configEnv[$envKey] = $value;
                }      
            ; break;
        }

        return true;
    }

    public static function getViewEnv():array{
        return self::$viewEnv;
    }

    public static function getConfigEnv():array{
        return self::$configEnv;
    }

    public static function getIsJsonEnv():bool{
        return self::$isJsonEnv;
    }
}