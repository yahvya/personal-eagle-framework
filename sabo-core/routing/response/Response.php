<?php

namespace SaboCore\Routing\Response;

/**
 * @brief Gestionnaire de retour de réponse
 * @author yahaya bathily https://github.com/yahvya
 */
class Response{
    /**
     * @var ResponseCode code retour http par défaut 200
     */
    protected ResponseCode $responseCode = ResponseCode::OK;

    /**
     * @var mixed|null contenu de la réponse
     */
    protected mixed $content = null;

    /**
     * @var array<string,string> en-têtes
     */
    protected array $headers = [
        "X-Content-Type-Options" => "nosniff",
        "Cache-Control" => "no-cache, no-store, must-revalidate",
        "Strict-Transport-Security" => "max-age=31536000; includeSubDomains"
    ];

    /**
     * @brief Ajoute un en-tête à la réponse
     * @param string $name nom de l'en-tête
     * @param string $value valeur associée
     * @return $this
     */
    public function setHeader(string $name,string $value):Response{
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * @brief Met à jour le contenu de la réponse
     * @param mixed $content contenu de la réponse
     * @return $this
     */
    public function setContent(mixed $content):Response{
        $this->content = $content;

        return $this;
    }

    /**
     * @brief Met à jour le code réponse
     * @param ResponseCode $code code réponse
     * @return $this
     */
    public function setResponseCode(ResponseCode $code):Response{
        $this->responseCode = $code;

        return $this;
    }

    /**
     * @return array<string,string> les en-têtes
     */
    public function getHeaders():array{
        return $this->headers;
    }

    /**
     * @return mixed le contenu de la réponse
     */
    public function getContent():mixed{
        return $this->content;
    }

    /**
     * @return ResponseCode le code réponse
     */
    public function getResponseCode():ResponseCode{
        return $this->responseCode;
    }

    /**
     * @brief Rend le contenu de la réponse
     * @return never
     */
    protected function renderContent():never{
        die();
    }

    /**
     * @brief Rend la réponse entière
     * @return never
     */
    public function renderResponse():never{
        @http_response_code(response_code: $this->responseCode->value);

        foreach($this->headers as $name => $header)
            header(header: "$name: $header");

        $this->renderContent();
    }
}