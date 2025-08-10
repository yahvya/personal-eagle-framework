<?php

namespace Yahvya\EagleFramework\Database\Default\Formatters;

use Override;
use Yahvya\EagleFramework\Database\Default\System\MysqlModel;

/**
 * @brief Reconstructeur de donnée json en chaine json
 * @author yahaya bathily https://github.com/yahvya
 */
class JsonReformer implements Formater
{
    #[Override]
    public function format(MysqlModel $baseModel, mixed $data): array
    {
        if (!json_validate(json: $data))
            throw new FormaterException(failedFormater: $this, errorMessage: "La donnée fournie n'est pas une chaine json", isDisplayable: false);

        $json = @json_decode(json: $data, associative: true);

        if ($json === null)
            throw new FormaterException(failedFormater: $this, errorMessage: "Echec de décodage du json", isDisplayable: false);

        return $json;
    }
}