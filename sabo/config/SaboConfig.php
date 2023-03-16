<?php

namespace Sabo\Config;

use Exception;

/**
 * permet la configuration du framework
 */
abstract class SaboConfig{
    private static $boolAttributes = [];
    private static $strAttributes = [];

    public static function setDefaultConfigurations():void{
        // configuration booléennes
        self::$boolAttributes = [
            SaboConfigAttributes::DEBUG_MODE->value => false,
            SaboConfigAttributes::INIT_WITH_DATABASE_CONNEXION->value => false
        ];

        // configuration des chaines
        self::$strAttributes = [
                
        ];
    }

    /**
     * affecte un status de type booléen
     * @param config_type le case de la donnée à mettre à jour (ex: SaboConfigAttributes::DEBUG_MODE)
     * @param status le status à affecter
     * @throws Exception en cas de donnée non existante
     */
    public static function setBoolConfig(SaboConfigAttributes $config_type,bool $status):void{
        if(!array_key_exists($config_type->value,self::$boolAttributes) ) throw new Exception("La clé {$config_type} n'existe pas dans les configurations de type booléen");

        self::$boolAttributes[$config_type->value] = $status;
    }

    /**
     * @param config_type le case de la donnée à récupérer (ex: SaboConfigAttributes::DEBUG_MODE)
     * @return bool le status de la donnée
     * @throws Exception en cas de donnée non existante
     */
    public static function getBoolConfig(SaboConfigAttributes $config_type):bool{
        if(!array_key_exists($config_type->value,self::$boolAttributes) ) throw new Exception("La clé {$config_type} n'existe pas dans les configurations de type booléen");

        return self::$boolAttributes[$config_type->value];
    }
    
    /**
     * affecte un status de type chaine
     * @param config_type le case de la donnée à mettre à jour (ex: SaboConfigAttributes::USER_AUTOLOAD_FILEPATH)
     * @param status le status à affecter
     * @throws Exception en cas de donnée non existante
     */
    public static function setStrConfig(SaboConfigAttributes $config_type,string $value):void{
        if(!array_key_exists($config_type->value,self::$strAttributes) ) throw new Exception("La clé {$config_type} n'existe pas dans les configurations de type chaine");

        self::$strAttributes[$config_type->value] = $value;
    }

    /**
     * @param config_type le case de la donnée à récupérer (ex: SaboConfigAttributes::USER_AUTOLOAD_FILEPATH)
     * @return bool le status de la donnée
     * @throws Exception en cas de donnée non existante
     */
    public static function getStrConfig(SaboConfigAttributes $config_type):string{
        if(!array_key_exists($config_type->value,self::$strAttributes) ) throw new Exception("La clé {$config_type} n'existe pas dans les configurations de type chaine");

        return self::$strAttributes[$config_type->value];
    }

    /**
     * @return array les données de configurations
     */
    public static function getConfig():array{
        return array_merge(self::$boolAttributes,self::$strAttributes);
    }
}