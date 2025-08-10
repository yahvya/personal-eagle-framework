<?php

namespace Yahvya\EagleFramework\Routing\Response;

use Override;

/**
 * @brief Json data response
 */
class JsonResponse extends Response
{
    /**
     * @param array $json Json content to provide
     */
    public function __construct(array $json)
    {
        $this->content = $json;

        $this->setHeader(name: "Content-Type", value: "application/json");
    }

    #[Override]
    public function renderContent(): never
    {
        $jsonContent = @json_encode(value: $this->content);

        die(!$jsonContent ? "{}" : $jsonContent);
    }
}