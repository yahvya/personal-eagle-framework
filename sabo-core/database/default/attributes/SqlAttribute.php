<?php

namespace SaboCore\Database\Default\Attributes;

/**
 * @brief Représente un attribut pouvant être créé en sql
 * @author yahaya bathily https://github.com/yahvya
 */
abstract class SqlAttribute{
    /**
     * @return string Fourni le sql de création de l'attribut
     */
    public abstract function getCreationSql():string;
}