<?php

namespace Sabo\Model\Model;

use Exception;
use ReflectionClass;
use Sabo\Model\Exception\ModelAttributeException;
use Sabo\Config\SaboConfig;
use Sabo\Config\SaboConfigAttributes;
use Sabo\Model\Attribute\TableColumn;
use Sabo\Model\Attribute\TableName;
use Sabo\Model\System\Interface\System;
use Sabo\Model\System\Mysql\SaboMysql;

/**
 * parent des modèles
 */
abstract class SaboModel implements System{
    use SaboMysql;

    /**
     * reflection de ce model
     */
    private ReflectionClass $reflection;

    /**
     * représente la configuration du model enfant
     */
    private array $columnsConfiguration;

    /**
     * nom de la table lié au model
     */
    private string $tableName;

    public function __construct(bool $createNewCon = false){            
        $this->myCon = $createNewCon ? self::getNewCon() : self::$sharedCon;

        $this->readChildConfiguration();
    }

    /**
     * tente d'assigner une valeur à un attribut du model
     * @param attributeName le nom de l'attribut
     * @param data la donnée à assigner
     * @return SaboModel this
     * @throws ModelAttributeException si une des conditions de vérification de l'attribut n'est pas valide
     * @throws Exception si l'attribut n'existe pas (phase de développement)
     */
    public function setAttribute(string $attributeName,mixed $data):SaboModel{
        // vérification de l'existance de l'attribut
        if(!$this->reflection->hasProperty($attributeName) ){
            if(SaboConfig::getBoolConfig(SaboConfigAttributes::DEBUG_MODE) ) 
                throw new Exception("L'attribut ({$attributeName}) n'existe pas sur la class " . $this->reflection->getName() );
            else 
                return $this;
        }

        // vérification des conditions
        $conds = $this->columnsConfiguration[$attributeName]["configClass"]->getConds();

        foreach($conds as $cond){
            if(!$cond->checkCondWith($data) ) throw new ModelAttributeException($cond);
        }

        $this->{$attributeName} = $data;

        return $this;
    }

    /**
     * lis les attributs associés au model enfant pour en tirer les informations
     */
    private function readChildConfiguration():void{
        $this->reflection = new ReflectionClass($this);

        // récupération du nom de la table lié
        $reflectionAttribute = $this->reflection->getAttributes(TableName::class);

        if(empty($reflectionAttribute[0]) ) throw new Exception("Attribut TableName manquant sur le model -> " . $this->reflection->getName() );

        $this->tableName = $reflectionAttribute[0]->newInstance()->getTableName();
        $this->columnsConfiguration = [];


        // récupération de la configuration des attributs
        foreach($this->reflection->getProperties() as $reflectionProperty){
            $reflectionAttribute = $reflectionProperty->getAttributes(TableColumn::class);

            if(!empty($reflectionAttribute[0]) ){
                $modelAttributeName = $reflectionProperty->getName();

                if($reflectionProperty->isPrivate() ) throw new Exception("Un attribut lié à une colonne de la base de données doit être public ou protected - Table({$this->tableName}) - Attribut({$modelAttributeName})");

                $this->columnsConfiguration[$modelAttributeName] = [
                    "name" => $modelAttributeName,
                    "configClass" => $reflectionAttribute[0]->newInstance() 
                ];
            }
        }
    }
}