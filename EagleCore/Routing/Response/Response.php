<?php

namespace Yahvya\EagleFramework\Routing\Response;

/**
 * @brief Response return manager
 */
class Response
{
    /**
     * @var ResponseCode default HTTP return code (200)
     */
    protected ResponseCode $responseCode = ResponseCode::OK;

    /**
     * @var mixed|null response content
     */
    protected mixed $content = null;

    /**
     * @var array{string:string} headers
     */
    protected(set) array $headers = [
        "X-Content-Type-Options" => "nosniff",
        "Cache-Control" => "no-cache, no-store, must-revalidate",
        "Strict-Transport-Security" => "max-age=31536000; includeSubDomains"
    ];

    /**
     * @brief Adds a header to the response
     * @param string $name header name
     * @param string $value associated value
     * @return $this
     */
    public function setHeader(string $name, string $value): Response
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * @brief Updates the response content
     * @param mixed $content response content
     * @return $this
     */
    public function setContent(mixed $content): Response
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @brief Updates the response code
     * @param ResponseCode $code response code
     * @return $this
     */
    public function setResponseCode(ResponseCode $code): Response
    {
        $this->responseCode = $code;

        return $this;
    }

    /**
     * @brief Renders the response content
     * @return never
     */
    protected function renderContent(): never
    {
        die();
    }

    /**
     * @brief Renders the entire response
     * @return never
     */
    public function renderResponse(): never
    {
        @http_response_code(response_code: $this->responseCode->value);

        foreach ($this->headers as $name => $header)
            header(header: "$name: $header");

        $this->renderContent();
    }
}
