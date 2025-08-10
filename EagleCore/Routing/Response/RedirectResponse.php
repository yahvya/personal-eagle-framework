<?php

namespace Yahvya\EagleFramework\Routing\Response;

/**
 * @brief Redirection response
 */
class RedirectResponse extends Response
{
    /**
     * @param string $link Redirection link
     */
    public function __construct(string $link)
    {
        $this->setHeader(name: "Location", value: $link);
    }
}