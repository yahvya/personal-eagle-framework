<?php

namespace SaboCore\Database\Default\Attributes;

use Attribute;
use Override;
use PDO;

/**
 * @brief Champs de type Booléen
 * @author yahaya bathily https://github.com/yahvya
 */
#[Attribute]
class BoolColumn extends TinyIntColumn {
    #[Override]
    public function getColumnType(): int{
        return PDO::PARAM_BOOL;
    }
}