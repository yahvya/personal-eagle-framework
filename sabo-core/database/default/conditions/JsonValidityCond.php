<?php

namespace SaboCore\Database\Default\Conditions;

use Override;
use SaboCore\Database\Default\System\MysqlModel;

/**
 * @brief Condition de vérification de validité json
 * @author yahaya bathily https://github.com/yahvya
 */
class JsonValidityCond implements Cond{
    #[Override]
    public function verifyData(MysqlModel $baseModel,string $attributeName,mixed $data): bool{
        return is_array(value: $data);
    }

    #[Override]
    public function getErrorMessage(): string{
        return "Json invalide";
    }

    #[Override]
    public function getIsDisplayable(): bool{
        return false;
    }
}