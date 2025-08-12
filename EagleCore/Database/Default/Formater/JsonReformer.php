<?php

namespace Yahvya\EagleFramework\Database\Default\Formater;

use Override;
use Yahvya\EagleFramework\Database\Default\System\MysqlModel;

/**
 * @brief JSON rebuild formater (reformer)
 */
class JsonReformer implements Formater
{
    #[Override]
    public function format(MysqlModel $baseModel, mixed $data): array
    {
        if (!json_validate(json: $data))
            throw new FormaterException(failedFormater: $this, errorMessage: "The provided data isn't a valid json string", isDisplayable: false);

        $json = @json_decode(json: $data, associative: true);

        if ($json === null)
            throw new FormaterException(failedFormater: $this, errorMessage: "Fail to decode the json string", isDisplayable: false);

        return $json;
    }
}