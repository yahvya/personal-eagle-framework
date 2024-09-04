<?php

namespace SaboCore\Routing\Response;

use Override;

/**
 * @brief json response
 */
class JsonResponse extends Response{
    /**
     * @param array $json json content
     */
    public function __construct(array $json){
        $this->content = $json;

        $this->setHeader(name: "Content-Type",value: "application/json");
    }

    #[Override]
    public function render():void{
        $jsonContent = @json_encode(value: $this->content);

        echo !$jsonContent ? "{}" : $jsonContent;
    }
}