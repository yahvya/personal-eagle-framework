<?php

namespace Yahvya\EagleFramework\Database\Default\Formatters;

use Override;
use Yahvya\EagleFramework\Database\Default\System\MysqlModel;

/**
 * @brief Formateur de donnée json en chaine json
 * @author yahaya bathily https://github.com/yahvya
 */
class JsonFormatter implements Formater
{
    #[Override]
    public function format(MysqlModel $baseModel, mixed $data): string
    {
        $json = @json_encode(value: $data);

        if ($json === false)
            throw new FormaterException(failedFormater: $this, errorMessage: "Echec de conversion json", isDisplayable: false);

        return $json;
    }
}