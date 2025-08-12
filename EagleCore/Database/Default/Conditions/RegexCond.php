<?php

namespace Yahvya\EagleFramework\Database\Default\Conditions;

use Attribute;
use Override;
use Yahvya\EagleFramework\Database\Default\System\MysqlModel;

/**
 * @brief Regex validation condition attribute
 */
#[Attribute]
class RegexCond implements Cond
{
    /**
     * @param string $regex Regular expression
     * @param string $errorMessage Validation error message
     * @param string $regexOptions Regex supplementary options. Placed at the end of the expression
     * @param string $delimiter Regex delimiters Délimiteurs de la regex (1 character)
     */
    public function __construct(
        protected(set) string $regex,
        protected(set) string $errorMessage {
            get => $this->errorMessage;
        },
        protected(set) string $regexOptions = "",
        protected(set) string $delimiter = "#"
    )
    {
        $this->delimiter = strlen($delimiter) == 1 ? $delimiter : "#";
    }

    #[Override]
    public function verifyData(MysqlModel $baseModel, string $attributeName, mixed $data): bool
    {
        return @preg_match(pattern: $this->delimiter . $this->regex . $this->delimiter . $this->regexOptions, subject: $data);
    }

    public bool $isDisplayable {
        get => true;
    }
}