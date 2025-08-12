<?php

namespace Yahvya\EagleFramework\Database\Default\Conditions;

use Attribute;
use DateTime;
use Override;
use Yahvya\EagleFramework\Database\Default\System\MysqlModel;
use Throwable;

/**
 * @brief Datetime field check condition
 */
#[Attribute]
class DateTimeCond implements Cond
{
    /**
     * @param string $errorMessage Error message
     */
    public function __construct(
        protected(set) string $errorMessage = "A well formated date is expected" {
            get => $this->errorMessage;
        }
    )
    {
    }

    #[Override]
    public function verifyData(MysqlModel $baseModel, string $attributeName, mixed $data): bool
    {
        try
        {
            new DateTime(datetime: $data);

            return true;
        }
        catch (Throwable)
        {
        }

        return false;
    }

    public bool $isDisplayable {
        get => true;
    }
}