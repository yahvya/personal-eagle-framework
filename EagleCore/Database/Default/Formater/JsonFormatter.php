<?php

namespace Yahvya\EagleFramework\Database\Default\Formater;

use Override;
use Yahvya\EagleFramework\Database\Default\System\MysqlModel;

/**
 * @brief JSON encode to store formater
 */
class JsonFormatter implements Formater
{
    #[Override]
    public function format(MysqlModel $baseModel, mixed $data): string
    {
        $json = @json_encode(value: $data);

        if ($json === false)
            throw new FormaterException(failedFormater: $this, errorMessage: "Fail to encode the json data", isDisplayable: false);

        return $json;
    }
}