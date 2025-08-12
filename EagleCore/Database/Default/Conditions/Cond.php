<?php

namespace Yahvya\EagleFramework\Database\Default\Conditions;

use Yahvya\EagleFramework\Database\Default\System\MysqlModel;

/**
 * @brief Represent a validation condition / assertion
 */
interface Cond
{
    /**
     * @brief Check if the data is valid
     * @param MysqlModel $baseModel Model instance
     * @param string $attributeName Attribute name
     * @param mixed $data Data to check
     * @return bool If the data is valid
     */
    public function verifyData(MysqlModel $baseModel, string $attributeName, mixed $data): bool;

    protected(set) string $errorMessage {
        get;
    }

    protected(set) bool $isDisplayable {
        get;
    }
}