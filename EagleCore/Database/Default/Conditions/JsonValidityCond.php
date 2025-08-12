<?php

namespace Yahvya\EagleFramework\Database\Default\Conditions;

use Attribute;
use Override;
use Yahvya\EagleFramework\Database\Default\System\MysqlModel;

/**
 * @brief JSON validity check
 */
#[Attribute]
class JsonValidityCond implements Cond
{
    #[Override]
    public function verifyData(MysqlModel $baseModel, string $attributeName, mixed $data): bool
    {
        return is_array(value: $data);
    }

    public bool $isDisplayable {
        get => false;
    }

    public string $errorMessage {
        get => "Invalid json";
    }
}