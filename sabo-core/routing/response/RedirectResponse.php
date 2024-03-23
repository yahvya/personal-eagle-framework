<?php

namespace SaboCore\Routing\Response;

/**
 * @brief Réponse redirection
 * @author yahaya bathily https://github.com/yahvya
 */
class RedirectResponse extends Response{
    /**
     * @param string $link lien de redirection
     */
    public function __construct(string $link){
        $this->setHeader(name: "Location",value: $link);
    }
}