<?php

namespace SaboCore\Database\Default\Attributes;

use SaboCore\Database\Default\Conditions\Cond;
use SaboCore\Database\Default\Conditions\MysqlCondException;
use SaboCore\Database\Default\Formatters\Formater;
use SaboCore\Database\Default\Formatters\FormaterException;

/**
 * @brief Représentation d'une colonne
 * @author yahaya bathily https://github.com/yahvya
 */
abstract class TableColumn extends SqlAttribute{
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
     * @param bool $isForeignKey si le champ est une clé étrangère
     * @param string|null $referencedModel Class du modèle référencé par la clé
     * @param string|null $referencedAttributeName Nom de l'attribut référencé
     * @param Cond[] $setConditions Conditions à vérifier sur la donnée originale avant de l'accepter dans l'attribut
     * @param Formater[] $dataFormatters Formateur de donnée pour transformer la donnée originale
     * @param Formater[] $datasReformers Formateur de donnée pour reformer la donnée
     * @attention Les conditions sont appelées avant formatage sur la donnée originale
     * @attention Chaque formateur recevra le résultat du précédent
     */
    public function __construct(string $columnName,bool $isNullable = false,bool $isPrimaryKey = false,bool $isUnique = false,bool $isForeignKey = false,?string $referencedModel = null,?string $referencedAttributeName = null,array $setConditions = [],array $dataFormatters = [],array $datasReformers = []){
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
     * @param mixed $data La donnée à vérifier
     * @return $this
     * @throws MysqlCondException en cas de condition invalide
     */
    public function verifyData(mixed $data):TableColumn{
        if($this->isNullable && $data === null)
            return $this;

        foreach($this->setConditions as $cond){
            if(!$cond->verifyData(data: $data))
                throw new MysqlCondException(failedCond: $cond);
        }

        return $this;
    }

    /**
     * @brief Formate la donnée originale en passant par les formateurs
     * @param mixed $originalData Donnée originale
     * @return mixed La donnée totalement formatée
     * @attention Les conditions doivent être vérifiées avant formatage
     * @throws FormaterException en cas d'erreur de formatage
     */
    public function formatData(mixed $originalData):mixed{
        if($originalData === null)
            return null;

        $formatedData = $originalData;

        foreach($this->datasFormatters as $formatter)
            $formatedData = $formatter->format(data: $formatedData);

        return $formatedData;
    }

    /**
     * @brief Reforme la donnée originale en passant par les reconstructeurs
     * @param mixed $formatedData Donnée formatée
     * @return mixed La donnée totalement reformée
     */
    public function reformData(mixed $formatedData):mixed{
        $reformedData = $formatedData;

        foreach($this->datasReformers as $formatter)
            $reformedData = $formatter->format(data: $formatedData);

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
     * @return Formater[] formateurs de données
     */
    public function getDatasFormatters(): array
    {
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
}