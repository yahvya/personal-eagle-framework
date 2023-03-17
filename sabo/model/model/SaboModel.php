<?php

namespace Sabo\Model\Model;

use PDO;
use Sabo\Model\System\Mysql\SaboMysql;

/**
 * parent des modèles
 */
abstract class SaboModel{
    use SaboMysql;

    public function __construct(bool $createNewCon = false){            
        $this->myCon = $createNewCon ? self::getNewCon() : self::$sharedCon;
    }
}