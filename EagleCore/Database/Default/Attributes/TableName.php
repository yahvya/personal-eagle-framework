<?php

namespace Yahvya\EagleFramework\Database\Default\Attributes;

use Attribute;
use Override;

#[Attribute]
class TableName extends SqlAttribute
{
    /**
     * @param string $tableName Table name
     */
    public function __construct(protected(set) string $tableName)
    {
    }

    #[Override]
    public function getCreationSql(): string
    {
        return "CREATE TABLE $this->tableName";
    }
}