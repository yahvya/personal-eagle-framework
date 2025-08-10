<?php

namespace Yahvya\EagleFramework\Routing\Response;

use Override;

/**
 * @brief Html response
 */
class HtmlResponse extends Response
{
    /**
     * @param string $content The HTML string to render
     */
    public function __construct(string $content)
    {
        $this->content = $content;

        $this->setHeader(name: "Content-Type", value: "text/html; charset=UTF-8");
    }

    #[Override]
    public function renderContent(): never
    {
        die($this->content);
    }
}