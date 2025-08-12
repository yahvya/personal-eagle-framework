<?php

namespace Yahvya\EagleFramework\Database\Default\Formater;

use Yahvya\EagleFramework\Database\Default\System\MysqlModel;

/**
 * @brief Data formater
 */
interface Formater
{
    /**
     * @brief Format the provided data
     * @param MysqlModel $baseModel Base model
     * @param mixed $data Data to format
     * @return mixed Formated result
     * @throws FormaterException On error
     */
    public function format(MysqlModel $baseModel, mixed $data): mixed;
}