<?php

namespace Yahvya\EagleFramework\Database\Default\Conditions;

use Override;
use Yahvya\EagleFramework\Database\Default\Conditions\Cond;
use Yahvya\EagleFramework\Database\Default\System\MysqlModel;
use Throwable;

/**
 * @brief Enum validation condition
 */
class EnumValidityCond implements Cond
{
    /**
     * @param string $errorMessage Error message
     * @param bool $isDisplayable If the message can be displayed
     */
    public function __construct(
        protected(set) string $errorMessage = "Invalid value" {
            get => $this->errorMessage;
        },
        protected(set) bool $isDisplayable {
            get => $this->isDisplayable;
        }
    )
    {
    }

    #[Override]
    public function verifyData(MysqlModel $baseModel, string $attributeName, mixed $data): bool
    {
        try
        {
            $possibleValues = $baseModel->getColumnConfig(attributName: $attributeName)->possibleValues->toArray();
            return
                in_array(needle: $data, haystack: $possibleValues) ||
                is_numeric(value: $data) && array_key_exists(key: $data, array: $possibleValues);
        }
        catch (Throwable)
        {
            return false;
        }
    }

}
