<?php

namespace SaboCore\Database\Default\Attributes;

use Attribute;
use Override;
use SaboCore\Database\Default\Conditions\Cond;
use SaboCore\Database\Default\Formatters\Formater;

/**
 * @brief Champs de type varchar
 * @author yahaya bathily https://github.com/yahvya
 */
#[Attribute]
class VarcharColumn extends TableColumn{
    /**
     * @var int Taille max du champ
     */
    protected int $maxLen;

    /**
     * @param string $columnName Nom de la colonne en base de donnée
     * @param int $maxLen Taille max du champ
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
    public function __construct(string $columnName,int $maxLen, bool $isNullable = false, bool $isPrimaryKey = false,bool $isUnique = false, bool $isForeignKey = false, ?string $referencedModel = null,?string $referencedAttributeName = null, array $setConditions = [], array $dataFormatters = [],array $datasReformers = []){
        parent::__construct(
            columnName: $columnName,
            isNullable: $isNullable,
            isPrimaryKey: $isPrimaryKey,
            isUnique: $isUnique,
            isForeignKey: $isForeignKey,
            referencedModel: $referencedModel,
            referencedAttributeName: $referencedAttributeName,
            setConditions: $setConditions,
            dataFormatters: $dataFormatters,
            datasReformers: $datasReformers
        );

        $this->maxLen = $maxLen;
    }

    #[Override]
    public function getCreationSql(): string{
        return
            "$this->columnName VARCHAR($this->maxLen)"
            . ($this->isNullable ? "" : " NOT NULL")
            .($this->isUnique() ? " UNIQUE": "");
    }
}