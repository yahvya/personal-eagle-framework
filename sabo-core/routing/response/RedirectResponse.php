<?php

namespace SaboCore\Routing\Response;

/**
 * @brief redirect response
 */
class RedirectResponse extends Response{
    /**
     * @param string $link link to redirect on
     */
    public function __construct(string $link){
        $this->setHeader(name: "Location",value: $link);
    }
}