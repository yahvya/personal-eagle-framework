<?php

namespace Yahvya\EagleFramework\Database\Default\Conditions;

use Attribute;
use Closure;
use Override;
use Yahvya\EagleFramework\Database\Default\System\MysqlModel;

/**
 * @brief Callable condition
 */
#[Attribute]
class CallableCond implements Cond
{
    /**
     * @param array|Closure $toVerify The callable to check
     * @param string $errorMessage Error message
     * @param bool $isDisplayable If the error can be displayed to the user
     */
    public function __construct(
        protected(set) array|Closure $toVerify,
        protected(set) string $errorMessage {
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
        return call_user_func(callback: $this->toVerify, args: $data);
    }
}
