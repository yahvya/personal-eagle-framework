<?php

namespace Sabo\Model\Model;

use Exception;
use ReflectionClass;
use Sabo\Model\Exception\ModelCondException;
use Sabo\Config\SaboConfig;
use Sabo\Config\SaboConfigAttributes;
use Sabo\Model\Attribute\JoinedColumn;
use Sabo\Model\Attribute\TableColumn;
use Sabo\Model\Attribute\TableName;
use Sabo\Model\System\Mysql\SaboMysql;
use TypeError;

/**
 * parent des modèles
 */
abstract class SaboModel extends SaboMysql{

    /**
     * reflection de ce model
     */
    private ?ReflectionClass $reflection = null;

    /**
     * représente la configuration du model enfant
     */
    private array $columnsConfiguration;

    /**
     * représente la configuration des liaisons model
     */
    private array $joinedLinks;

    /**
     * nom de la table lié au model
     */
    private string $tableName;

    protected function pseudoConstruct():void{
        $this
            ->readChildConfiguration()
            ->readJoinedLinks();
    }

    /**
     * @return array les données du modèles sous forme de tableau ou null si aucune donné initialisé, les données non initialisé sont mise à null
     */
    public function getAsArray():array{
        $modelData = [];

        foreach($this->columnsConfiguration as $attributeName => $configuration){
            if(!empty($configuration["configClass"]) ) $modelData[$attributeName] = $configuration["reflection"]->isInitialized($this) ? $this->$attributeName : null;
        }

        return $modelData;  
    }

    /**
     * tente d'assigner une valeur à un attribut du model
     * @param attributeName le nom de l'attribut
     * @param data la donnée à assigner
     * @return SaboModel this
     * @throws ModelCondException si une des conditions de vérification de l'attribut n'est pas valide
     * @throws Exception si l'attribut n'est pas accessible (phase de développement)
     */
    public function setAttribute(string $attributeName,mixed $data):SaboModel{
        // vérification de l'existance de l'attribut
        if(!$this->checkAttributeAccessible($attributeName) ) return $this;

        if($this->columnsConfiguration[$attributeName]["configClass"]->getIsNullable() && $data == null){
            $this->{$attributeName} = $data;
            
            return $this;
        }

        // vérification des conditions dans le cas où c'est un champs lié à la base de donnée
        if(!empty($this->columnsConfiguration[$attributeName]["haveToCheckCond"]) ){
            // vérification des conditions
            $conds = $this->columnsConfiguration[$attributeName]["configClass"]->getConds();

            foreach($conds as $cond){
                try{
                    if(!$cond->checkCondWith($data) ) throw new ModelCondException($cond);
                }
                catch(TypeError){
                    throw new ModelCondException($cond);
                }
            }
        }

        $this->{$attributeName} = $data;

        return $this;
    }

    /**
     * tente de récupérer la valeur d'un attribut
     * @param attributeName 
     * @return mixed la valeur de l'attribut ou null si non existant
     * @throws Exception si l'attribut n'est pas accessible (phase de développement)
     */
    public function getAttribute(string $attributeName):mixed{
        // vérification de l'existance de l'attribut
        return !$this->checkAttributeAccessible($attributeName) ? null : $this->{$attributeName};
    }   

    /**
     * @return array la configuration des attributs
     */
    public function getColumnsConfiguration():array{
        return $this->columnsConfiguration;
    }

    /**
     * @return array la configuration jointures
     */
    public function getJoinedLinks():array{
        return $this->joinedLinks;
    }

    /**
     * @return string le nom de la table lié
     */
    public function getTableName():string{
        return $this->tableName;
    }

    /**
     * @return ReflectionClass la classe de reflection
     */
    public function getReflection():ReflectionClass{
        return $this->reflection;
    }   

    /**
     * vérifie qu'un attribut existe
     * @param attributeName le nom de l'attribut
     * @return bool si l'attribut existe
     * @throws Exception si l'attribut n'est pas accessible pas (phase de développement)
     */
    private function checkAttributeAccessible(string $attributeName):bool{
        // vérification de l'existance de l'attribut
        if(!$this->reflection->hasProperty($attributeName) || $this->columnsConfiguration[$attributeName]["reflection"]->isPrivate() ){
            if(SaboConfig::getBoolConfig(SaboConfigAttributes::DEBUG_MODE) ) 
                throw new Exception("L'attribut ({$attributeName}) n'existe pas ou est privé sur la class " . $this->reflection->getName() );
            else 
                return false;
        }

        return true;
    }   

    /**
     * lis les attributs associés au model enfant pour en tirer les informations
     * @return SaboModel this
     */
    private function readChildConfiguration():SaboModel{
        $this->reflection = new ReflectionClass($this);

        // récupération du nom de la table lié
        $reflectionAttribute = $this->reflection->getAttributes(TableName::class);

        if(empty($reflectionAttribute[0]) ) throw new Exception("Attribut TableName manquant sur le model -> " . $this->reflection->getName() );

        $this->tableName = $reflectionAttribute[0]->newInstance()->getTableName();
        $this->columnsConfiguration = [];


        // récupération de la configuration des attributs
        foreach($this->reflection->getProperties() as $reflectionProperty){
            $reflectionAttribute = $reflectionProperty->getAttributes(TableColumn::class);

            $modelAttributeName = $reflectionProperty->getName();
            
            if(!empty($reflectionAttribute[0]) ){
                if($reflectionProperty->isPrivate() ) throw new Exception("Un attribut lié à une colonne de la base de données doit être public ou protected - Table({$this->tableName}) - Attribut({$modelAttributeName})");

                $this->columnsConfiguration[$modelAttributeName] = [
                    "name" => $modelAttributeName,
                    "configClass" => $reflectionAttribute[0]->newInstance(),
                    "haveToCheckCond" => true,
                    "reflection" => $reflectionProperty
                ];
            }
            else{
                $this->columnsConfiguration[$modelAttributeName] = [
                    "name" => $modelAttributeName,
                    "reflection" => $reflectionProperty,
                    "haveToCheckCond" => false
                ];
            }
        }

        return $this;
    }

    /**
     * lis les attributs associés au model enfant pour en tirer les informations les jointures
     * @return SaboModel this
     */
    private function readJoinedLinks():SaboModel{
        if($this->reflection == null) $this->reflection = new ReflectionClass($this);

        $this->joinedLinks = [];

        // récupération des éléments
        foreach($this->reflection->getProperties() as $reflectionProperty){
            $reflectionAttributes = $reflectionProperty->getAttributes(JoinedColumn::class);

            if(empty($reflectionAttributes) ) continue;

            $this->joinedLinks[$reflectionProperty->getName()] = $reflectionAttributes[0]->newInstance();
        }

        return $this;
    }
}