<?php

namespace Yahvya\EagleFramework\Database\Default\Conditions;

use Attribute;
use Override;
use Yahvya\EagleFramework\Database\Default\System\MysqlModel;

/**
 * @brief filter_var function-based condition
 */
#[Attribute]
class FilterCond implements Cond
{
    /**
     * @param int $filter validations constant FILTER_VALIDATE_...
     * @param string $errorMessage Error message
     */
    public function __construct(
        protected(set) int $filter,
        protected(set) string $errorMessage {
            get => $this->errorMessage;
        }
    )
    {
    }

    #[Override]
    public function verifyData(MysqlModel $baseModel, string $attributeName, mixed $data): bool
    {
        return filter_var(value: $data, filter: $this->filter);
    }

    public bool $isDisplayable {
        get => true;
    }
}