<?php

namespace SaboCore\Database\Default\System;

use Override;
use ReflectionClass;
use SaboCore\Config\ConfigException;
use SaboCore\Database\Default\Attributes\EnumColumn;
use SaboCore\Database\Default\Attributes\TableColumn;
use SaboCore\Database\Default\Attributes\TableName;
use SaboCore\Database\Default\Conditions\MysqlCondException;
use SaboCore\Database\Default\Formatters\FormaterException;
use SaboCore\Database\System\DatabaseCondition;
use SaboCore\Database\System\DatabaseCondSeparator;
use SaboCore\Database\System\DatabaseModel;
use SaboCore\Utils\List\SaboList;

/**
 * @brief Modèle de la base de données mysql
 * @author yahaya bathily https://github.com/yahvya
 * @attention les attributs utilisables doivent être protected|public
 */
class MysqlModel extends DatabaseModel{
    /**
     * @var TableName Fournisseur du nom de la table
     */
    protected TableName $tableName;

    /**
     * @var TableColumn[] Configuration des colonnes de la base de donnée. Indicé par le nom de l'attribut et contient comme valeur l'instance de TableColumn
     */
    protected array $dbColumnsConfig;

    /**
     * @var array Valeur originale des attributs sans formatage
     */
    protected array $attributesOriginalValues = [];

    /**
     * @throws ConfigException en cas d'erreur de configuration du model
     */
    public function __construct(){
        $this->loadConfiguration();
    }

    #[Override]
    public function create(): bool{
        $this->beforeCreate();



        $this->afterCreate();

        return true;
    }

    #[Override]
    public function update(): bool{
        $this->beforeCreate();



        $this->afterCreate();

        return true;
    }

    #[Override]
    public function delete(): bool{
        $this->beforeCreate();



        $this->afterCreate();

        return true;
    }

    #[Override]
    public function afterGeneration(): DatabaseModel{
        parent::afterGeneration();

        // sauvegarde des valeurs par défaut des attributs

        foreach($this->dbColumnsConfig as $attributeName => $_)
            $this->attributesOriginalValues[$attributeName] = $this->$attributeName;

        return $this;
    }

    /**
     * @brief Met à jour la valeur d'un attribut
     * @param string $attributeName Nom de l'attribut à mettre à jour
     * @param mixed $value valeur à placer
     * @return $this
     * @throws ConfigException en cas d'attribut non trouvé
     * @throws FormaterException en cas d'erreur de formatage
     * @throws MysqlCondException en cas d'erreur de validation
     */
    public function setAttribute(string $attributeName,mixed $value):MysqlModel{
        $columnConfig = $this->dbColumnsConfig[$attributeName] ?? null;

        if($columnConfig === null)
            throw new ConfigException(message: "Attribut non trouvé");

        // vérification de la validité et formatage de la donnée
        $formatedData = $columnConfig
            ->verifyData(baseModel: $this,attributeName: $attributeName,data: $value)
            ->formatData(baseModel: $this,originalData: $value);

        $this->attributesOriginalValues[$attributeName] = $value;
        $this->$attributeName = $formatedData;

        return $this;
    }

    /**
     * @brief Fourni la valeur de l'attribut
     * @param string $attributeName nom de l'attribut
     * @param bool $reform si true reforme la donnée via les formateurs de reformation
     * @return mixed La donnée
     * @throws ConfigException en cas d'attribut non trouvé
     * @throws FormaterException en cas d'échec de formatage
     */
    public function getAttribute(string $attributeName,bool $reform = true):mixed{
        $columnConfig = $this->dbColumnsConfig[$attributeName] ?? null;

        if($columnConfig === null)
            throw new ConfigException(message: "Attribut non trouvé");

        $data = $this->$attributeName;

        // reformation de la donnée
        if($reform)
            $data = $columnConfig->reformData(baseModel: $this,formatedData: $data);

        return $data;
    }

    /**
     * @brief Fourni la valeur originale non formatée de l'attribut
     * @attention Si la valeur était inséré en base de données l'originale équivaut à la valeur formatée avant insertion
     * @param string $attributeName non de l'attribut
     * @return mixed la valeur ou null
     */
    public function getAttributOriginal(string $attributeName):mixed{
        return $this->attributesOriginalValues[$attributeName] ?? null;
    }

    /**
     * @return TableColumn[]|EnumColumn[] La configuration des colonnes
     */
    public function getColumnsConfig():array{
        return $this->dbColumnsConfig;
    }

    /**
     * @brief Fourni la configuration de colonne d'un attribut en particulier
     * @param string $attributName Nom de l'attribut
     * @return TableColumn|EnumColumn|null la configuration de colonne ou null
     */
    public function getColumnConfig(string $attributName):TableColumn|EnumColumn|null{
        return $this->dbColumnsConfig[$attributName] ?? null;
    }

    /**
     * @brief Charge la configuration du modèle
     * @return void
     * @throws ConfigException en cas de mauvaise configuration
     */
    protected function loadConfiguration():void{
        $reflection = new ReflectionClass(objectOrClass: $this);

        // récupération du nom de la table
        $found = false;

        foreach($reflection->getAttributes() as $attribute){
            if($attribute->getName() === TableName::class){
                $this->tableName = $attribute->newInstance();
                $found = true;
                break;
            }
        }

        if(!$found)
            throw new ConfigException(message: "Model mal configuré");

        // chargement des colonnes lié à la base de donnée
        $this->dbColumnsConfig = [];

        foreach($reflection->getProperties() as $property){
            // recherche de l'attribut descriptif
            foreach($property->getAttributes() as $attribute){
                $instance = $attribute->newInstance();

                if($instance instanceof TableColumn){
                    $this->dbColumnsConfig[$property->getName()] = $instance;
                    break;
                }
            }
        }
    }

    /**
     * @return TableName Fournisseur du nom de la table
     */
    public function getTableNameManager(): TableName{
        return $this->tableName;
    }

    /**
     * @param MysqlCondition|MysqlCondSeparator ...$findBuilders Configuration de recherche
     * @return MysqlModel|null model trouvé ou null
     */
    #[Override]
    public static function findOne(DatabaseCondition|DatabaseCondSeparator ...$findBuilders): DatabaseModel|null{
        return null;
    }

    /**
     * @param MysqlCondition|MysqlCondSeparator ...$findBuilders Configuration de recherche
     * @return SaboList<MysqlModel> Liste des occurrences
     */
    #[Override]
    public static function findAll(DatabaseCondition|DatabaseCondSeparator ...$findBuilders): SaboList{
        return new SaboList([]);
    }
}