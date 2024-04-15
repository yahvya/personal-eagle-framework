<?php

namespace SaboCore\Database\Default\Formatters;

/**
 * @brief Formateur de donnée json en chaine json
 * @author yahaya bathily https://github.com/yahvya
 */
class JsonFormatter implements Formater{
    public function format(mixed $data): string{
        $json = @json_encode(value: $data);

        if($json === false)
            throw new FormaterException(failedFormater: $this, errorMessage: "Echec de conversion json",isDisplayable: false);

        return $json;
    }
}