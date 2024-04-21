<?php

namespace SaboCore\Database\Default\Attributes;

use SaboCore\Database\Default\Conditions\Cond;
use SaboCore\Database\Default\Conditions\MysqlCondException;
use SaboCore\Database\Default\Formatters\Formater;
use SaboCore\Database\Default\Formatters\FormaterException;
use SaboCore\Database\Default\System\MysqlModel;

/**
 * @brief Représentation d'une colonne
 * @author yahaya bathily https://github.com/yahvya
 */
abstract class TableColumn extends SqlAttribute{
    /**
     * @brief Chaine représentant si l'attribut n'a pas de valeur par défaut
     */
    protected const string NO_DEFAULT_VALUE = "ATTRIBUTE_NO_DEFAULT_VALUE";

    /**
     * @var string Nom de la colonne
     */
    protected string $columnName;

    /**
     * @var bool Si la colonne est une clé primaire
     */
    protected bool $isPrimaryKey;

    /**
     * @var bool Si la colonne est une clé étrangère
     */
    protected bool $isForeignKey;

    /**
     * @var bool Si le champ est nullable
     */
    protected bool $isNullable;

    /**
     * @var bool Si le champ est unique
     */
    protected bool $isUnique;

    /**
     * @var string Valeur par défaut
     */
    protected string $defaultValue;

    /**
     * @var string|null Classe référencée par la clé étrangère
     */
    protected ?string $referencedModel;

    /**
     * @var string|null Nom de l'attribut référencé
     */
    protected ?string $referencedAttributeName;

    /**
     * @var Cond[] Conditions à vérifier avant affectation
     */
    protected array $setConditions;

    /**
     * @var Formater[] Formateurs de données avant sauvegarde
     */
    protected array $datasFormatters = [];

    /**
     * @var Formater[] Déformateurs de données pour la récupération
     */
    protected array $datasReformers = [];

    /**
     * @param string $columnName Nom de la colonne en base de donnée
     * @param bool $isNullable si le champ est nullable (mis à false par défaut si clé primaire)
     * @param bool $isPrimaryKey si le champ est une clé primaire
     * @param bool $isUnique si le champ est unique
     * @param string $defaultValue Valeur par défaut de l'attribut (sous la forme sql)
     * @param bool $isForeignKey si le champ est une clé étrangère
     * @param string|null $referencedModel Class du modèle référencé par la clé
     * @param string|null $referencedAttributeName Nom de l'attribut référencé
     * @param Cond[] $setConditions Conditions à vérifier sur la donnée originale avant de l'accepter dans l'attribut
     * @param Formater[] $dataFormatters Formateur de donnée pour transformer la donnée originale
     * @param Formater[] $datasReformers Formateur de donnée pour reformer la donnée
     * @attention Les conditions sont appelées avant formatage sur la donnée originale
     * @attention Chaque formateur recevra le résultat du précédent
     * @attention L'attribut par défaut doit contenir la chaine exacte qui sera saisie dans la création sql ex : "'default'" "10" ...
     */
    public function __construct(string $columnName,bool $isNullable = false,bool $isPrimaryKey = false,bool $isUnique = false,string $defaultValue = self::NO_DEFAULT_VALUE,bool $isForeignKey = false,?string $referencedModel = null,?string $referencedAttributeName = null,array $setConditions = [],array $dataFormatters = [],array $datasReformers = []){
        $this->defaultValue = $defaultValue;
        $this->columnName = $columnName;
        $this->isNullable = $isPrimaryKey ? false : $isNullable;
        $this->isPrimaryKey = $isPrimaryKey;
        $this->isForeignKey = $isForeignKey;
        $this->referencedModel = $isForeignKey ? $referencedModel : null;
        $this->setConditions = $setConditions;
        $this->datasFormatters = $dataFormatters;
        $this->datasReformers = $datasReformers;
        $this->isUnique = $isUnique;
        $this->referencedAttributeName = $referencedAttributeName;
    }

    /**
     * @brief Vérifie la donnée à affecter
     * @param MysqlModel $baseModel Model de base
     * @param string $attributeName Nom de l'attribut
     * @param mixed $data La donnée à vérifier
     * @return $this
     * @throws MysqlCondException en cas de condition invalide
     */
    public function verifyData(MysqlModel $baseModel,string $attributeName,mixed $data):TableColumn{
        if($this->isNullable && $data === null)
            return $this;

        foreach($this->setConditions as $cond){
            if(!$cond->verifyData(baseModel: $baseModel,attributeName: $attributeName,data: $data))
                throw new MysqlCondException(failedCond: $cond);
        }

        return $this;
    }

    /**
     * @brief Formate la donnée originale en passant par les formateurs
     * @param MysqlModel $baseModel Model de base
     * @param mixed $originalData Donnée originale
     * @return mixed La donnée totalement formatée
     * @attention Les conditions doivent être vérifiées avant formatage
     * @throws FormaterException en cas d'erreur de formatage
     */
    public function formatData(MysqlModel $baseModel,mixed $originalData):mixed{
        if($originalData === null)
            return null;

        $formatedData = $originalData;

        foreach($this->datasFormatters as $formatter)
            $formatedData = $formatter->format(baseModel: $baseModel,data: $formatedData);

        return $formatedData;
    }

    /**
     * @brief Reforme la donnée originale en passant par les reconstructeurs
     * @param MysqlModel $baseModel Model de base
     * @param mixed $formatedData Donnée formatée
     * @return mixed La donnée totalement reformée
     * @throws FormaterException en cas d'erreur de formatage
     */
    public function reformData(MysqlModel $baseModel,mixed $formatedData):mixed{
        $reformedData = $formatedData;

        foreach($this->datasReformers as $formatter)
            $reformedData = $formatter->format(baseModel: $baseModel,data: $formatedData);

        return $reformedData;
    }

    /**
     * @return string Nom de la colonne en base de donnée
     */
    public function getColumnName(): string{
        return $this->columnName;
    }

    /**
     * @return bool Si le champ est une clé primaire
     */
    public function isPrimaryKey(): bool{
        return $this->isPrimaryKey;
    }

    /**
     * @return bool Si le champ est une clé étrangère
     */
    public function isForeignKey(): bool{
        return $this->isForeignKey;
    }

    /**
     * @return bool Si le champ est nullable
     */
    public function isNullable(): bool{
        return $this->isNullable;
    }

    /**
     * @return bool Si le champ est unique
     */
    public function isUnique(): bool{
        return $this->isUnique;
    }

    /**
     * @return string|null Class du model référencé en cas de foreign key sinon null
     */
    public function getReferencedModel(): ?string{
        return $this->referencedModel;
    }

    /**
     * @return Cond[] Conditions de validation
     */
    public function getSetConditions(): array{
        return $this->setConditions;
    }

    /**
     * @return string La chaine sql de valeur par défaut ou vide si pas de valeur par défaut
     */
    public function getDefaultValueStr():string{
        return $this->haveDefaultValue() ? $this->defaultValue : "";
    }

    /**
     * @return bool Si l'attribut à une valeur par défaut
     */
    public function haveDefaultValue():bool{
        return $this->defaultValue !== self::NO_DEFAULT_VALUE;
    }

    /**
     * @return Formater[] formateurs de données
     */
    public function getDatasFormatters(): array{
        return $this->datasFormatters;
    }

    /**
     * @return Formater[] Reconstructeurs de données
     */
    public function getDatasReformers(): array{
        return $this->datasReformers;
    }

    /**
     * @return string|null Nom de l'attribut référencé ou null en cas de foreign key
     */
    public function getReferencedAttributeName(): ?string{
        return $this->referencedAttributeName;
    }

    /**
     * @brief Méthode appellé au montage de la propriété portant cet attribut. Transforme la donnée récupérée en base de donnée en la donnée finale
     * @param mixed $data La donnée de base
     * @return mixed la donnée convertie
     */
    public function convertToValue(mixed $data):mixed{
        return $data;
    }

    /**
     * @brief Méthode appellé à l'insertion ou mise à jour de la propriété portant cet attribut. Transforme la donnée de l'attribut en une donnée capable d'être inséré en base de donnée
     * @param mixed $data La donnée de base
     * @return mixed la donnée convertie
     */
    public function convertFromValue(mixed $data):mixed{
        return $data;
    }

    /**
     * @return int Fourni le type de paramètre pdo
     */
    abstract public function getColumnType():int;
}