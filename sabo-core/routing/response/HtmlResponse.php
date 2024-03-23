<?php

namespace SaboCore\Routing\Response;

use Override;

/**
 * @brief Réponse html
 * @author yahaya bathily https://github.com/yahvya
 */
class HtmlResponse extends Response{
    /**
     * @param string $content contenu html de la réponse
     */
    public function __construct(string $content){
        $this->content = $content;

        $this->setHeader(name: "Content-Type",value: "text/html; charset=UTF-8");
    }

    #[Override]
    public function renderContent(): never{
        die($this->content);
    }
}