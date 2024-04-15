<?php

namespace SaboCore\Database\Default\Formatters;

/**
 * @brief Formateur de donnée
 * @author yahaya bathily https://github.com/yahvya
 */
interface Formater{
    /**
     * @brief Formate la donnée fournie
     * @param mixed $data La donnée à formater
     * @return mixed Le résultat formaté
     * @throws FormaterException en cas d'erreur
     */
    public function format(mixed $data):mixed;
}