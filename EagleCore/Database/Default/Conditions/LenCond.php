<?php

namespace Yahvya\EagleFramework\Database\Default\Conditions;

use Attribute;
use Override;
use Yahvya\EagleFramework\Database\Default\System\MysqlModel;

/**
 * @brief String length check condition
 */
#[Attribute]
class LenCond implements Cond
{
    /**
     * @param int $minLength String min len
     * @param int $maxLength String max len
     * @param string $errorMessage Error message
     */
    public function __construct(
        protected(set) int $minLength = 1,
        protected(set) int $maxLength = 255,
        protected(set) string $errorMessage = "PLease check the provided string content." {
            get => $this->errorMessage;
        }
    )
    {
    }

    #[Override]
    public function verifyData(MysqlModel $baseModel, string $attributeName, mixed $data): bool
    {
        if (gettype(value: $data) == "string")
        {
            $len = strlen(string: $data);

            return $len >= $this->minLength && $len <= $this->maxLength;
        }

        return false;
    }

    public bool $isDisplayable {
        get => true;
    }
}