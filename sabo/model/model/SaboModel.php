<?php

namespace Sabo\Model\Model;

use PDO;
use Sabo\Model\System\Interface\System;
use Sabo\Model\System\Mysql\SaboMysql;

/**
 * parent des modèles
 */
abstract class SaboModel implements System{
    use SaboMysql;

    /**
     * représente la configuration du model enfant
     */
    private array $configuration;

    public function __construct(bool $createNewCon = false){            
        $this->myCon = $createNewCon ? self::getNewCon() : self::$sharedCon;

        $this->readChildConfiguration();
    }

    /**
     * lis les attributs associés au model enfant pour en tirer les informations
     */
    private function readChildConfiguration():void{
        echo "nouveau modèle";   
    }
}