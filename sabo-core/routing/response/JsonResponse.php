<?php

namespace SaboCore\Routing\Response;

use Override;

/**
 * @brief Réponse json
 * @author yahaya bathily https://github.com/yahvya
 */
class JsonResponse extends Response{
    /**
     * @param array $json contenu json
     */
    public function __construct(array $json){
        $this->content = $json;

        $this->setHeader(name: "Content-Type",value: "application/json");
    }

    #[Override]
    public function renderContent():never{
        $jsonContent = @json_encode(value: $this->content);

        die(!$jsonContent ? "{}" : $jsonContent);
    }
}