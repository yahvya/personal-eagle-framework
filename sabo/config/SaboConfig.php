<?php

namespace Sabo\Config;

use Exception;
use Sabo\DefaultPage\MessagePage;

/**
 * permet la configuration du framework
 */
abstract class SaboConfig{
    /**
     * liste des attributes booléens
     */
    private static array $boolAttributes = [];
    /**
     * liste des attributes chaines
     */
    private static array $strAttributes = [];
    /**
     * liste des attributes callable
     */
    private static array $callableAttributes = [];

    /**
     * défini la configuration par défaut de l'application
     */
    public static function setDefaultConfigurations():void{
        // configuration booléennes
        self::$boolAttributes = [
            SaboConfigAttributes::DEBUG_MODE->value => false,
            SaboConfigAttributes::INIT_WITH_DATABASE_CONNEXION->value => false
        ];

        // configuration des chaines
        self::$strAttributes = [
            SaboConfigAttributes::ENV_FILE_TYPE->value => "env.json",
            SaboConfigAttributes::BASIC_ENV_FORVIEW_PREFIX->value => "forview_"
        ];

        // configuration des callables
        self::$callableAttributes = [
            SaboConfigAttributes::NO_FOUND_DEFAULT_PAGE->value => [new MessagePage("Page non trouvé","La page que vous cherchez n'a pas été trouvé !"),"show"],
            SaboConfigAttributes::TECHNICAL_ERROR_DEFAULT_PAGE->value => [new MessagePage("Erreur technique","Une erreur technique s'est produite !"),"show"]
        ];
    }

    /**
     * affecte un status de type booléen
     * @param configType le case de la donnée à mettre à jour (ex: SaboConfigAttributes::DEBUG_MODE)
     * @param status le status à affecter
     * @throws Exception en cas de donnée non existante
     */
    public static function setBoolConfig(SaboConfigAttributes $configType,bool $status):void{
        if(!array_key_exists($configType->value,self::$boolAttributes) ) throw new Exception("La clé {$configType->value} n'existe pas dans les configurations de type booléen");

        self::$boolAttributes[$configType->value] = $status;
    }

    /**
     * @param configType le case de la donnée à récupérer (ex: SaboConfigAttributes::DEBUG_MODE)
     * @return bool le status de la donnée
     * @throws Exception en cas de donnée non existante
     */
    public static function getBoolConfig(SaboConfigAttributes $configType):bool{
        if(!array_key_exists($configType->value,self::$boolAttributes) ) throw new Exception("La clé {$configType->value} n'existe pas dans les configurations de type booléen");

        return self::$boolAttributes[$configType->value];
    }
    
    /**
     * affecte un status de type chaine
     * @param configType le case de la donnée à mettre à jour (ex: SaboConfigAttributes::USER_AUTOLOAD_FILEPATH)
     * @param value la chaine à affecter
     * @throws Exception en cas de donnée non existante
     */
    public static function setStrConfig(SaboConfigAttributes $configType,string $value):void{
        if(!array_key_exists($configType->value,self::$strAttributes) ) throw new Exception("La clé {$configType->value} n'existe pas dans les configurations de type chaine");

        self::$strAttributes[$configType->value] = $value;
    }

    /**
     * @param configType le case de la donnée à récupérer (ex: SaboConfigAttributes::USER_AUTOLOAD_FILEPATH)
     * @return string la chaine
     * @throws Exception en cas de donnée non existante
     */
    public static function getStrConfig(SaboConfigAttributes $configType):string{
        if(!array_key_exists($configType->value,self::$strAttributes) ) throw new Exception("La clé {$configType->value} n'existe pas dans les configurations de type chaine");

        return self::$strAttributes[$configType->value];
    }

    /**
     * affecte un status de type callable
     * @param configType le case de la donnée à mettre à jour (ex: SaboConfigAttributes::NO_FOUND_DEFAULT_PAGE)
     * @param callable le callable à affecter
     * @throws Exception en cas de donnée non existante
     */
    public static function setCallableConfig(SaboConfigAttributes $configType,array $callable):void{
        if(!array_key_exists($configType->value,self::$callableAttributes) ) throw new Exception("La clé {$configType->value} n'existe pas dans les configurations de type callable");
        if(!is_callable($callable) ) throw new Exception("Un Callable valide est attendue");

        self::$callableAttributes[$configType->value] = $callable;
    }

    /**
     * @param configType le case de la donnée à récupérer (ex: SaboConfigAttributes::NO_FOUND_DEFAULT_PAGE)
     * @return array le status de la donnée
     * @throws Exception en cas de donnée non existante ou de valeur non callable envoyée
     */
    public static function getCallableConfig(SaboConfigAttributes $configType):array{
        if(!array_key_exists($configType->value,self::$callableAttributes) ) throw new Exception("La clé {$configType->value} n'existe pas dans les configurations de type callable");

        return self::$callableAttributes[$configType->value];
    }

    /**
     * @return array les données de configurations
     */
    public static function getConfig():array{
        return array_merge(self::$boolAttributes,self::$strAttributes,self::$callableAttributes);
    }
}