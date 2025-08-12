<?php

namespace Yahvya\EagleFramework\Database\Default\Attributes;

use Attribute;
use Override;
use PDO;

/**
 * @brief Boolean column
 */
#[Attribute]
class BoolColumn extends TinyIntColumn
{
    #[Override]
    public function getColumnType(): int
    {
        return PDO::PARAM_BOOL;
    }
}