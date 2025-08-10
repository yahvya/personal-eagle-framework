<?php

namespace Yahvya\EagleFramework\Database\Default\Formatters;

use Yahvya\EagleFramework\Database\Default\System\MysqlModel;

/**
 * @brief Formateur de donnée
 * @author yahaya bathily https://github.com/yahvya
 */
interface Formater
{
    /**
     * @brief Formate la donnée fournie
     * @param MysqlModel $baseModel Model de base
     * @param mixed $data La donnée à formater
     * @return mixed Le résultat formaté
     * @throws FormaterException en cas d'erreur
     */
    public function format(MysqlModel $baseModel, mixed $data): mixed;
}