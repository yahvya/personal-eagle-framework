<?php

namespace SaboCore\Routing\Response;

/**
 * @brief Response manager
 */
class Response{
    /**
     * @var ResponseCode response code
     */
    protected ResponseCode $responseCode = ResponseCode::OK;

    /**
     * @var mixed|null response content
     */
    protected mixed $content = null;

    /**
     * @var array{string:string} headers
     */
    protected array $headers = [
        "X-Content-Type-Options" => "nosniff",
        "Cache-Control" => "no-cache, no-store, must-revalidate",
        "Strict-Transport-Security" => "max-age=31536000; includeSubDomains"
    ];

    /**
     * @brief add a header to the response
     * @param string $name header name
     * @param string $value associated value
     * @return $this
     */
    public function setHeader(string $name,string $value):static{
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * @brief set the response content
     * @param mixed $content response content
     * @return $this
     */
    public function setContent(mixed $content):static{
        $this->content = $content;

        return $this;
    }

    /**
     * @brief update the response code
     * @param ResponseCode $code response code
     * @return $this
     */
    public function setResponseCode(ResponseCode $code):static{
        $this->responseCode = $code;

        return $this;
    }

    /**
     * @return array{string:string} headers
     */
    public function getHeaders():array{
        return $this->headers;
    }

    /**
     * @return mixed response content
     */
    public function getContent():mixed{
        return $this->content;
    }

    /**
     * @return ResponseCode response code
     */
    public function getResponseCode():ResponseCode{
        return $this->responseCode;
    }

    /**
     * @brief render content
     * @return void
     */
    public function render():void{}

    /**
     * @brief render entire response
     * @return void
     */
    public function renderResponse():void{
        @http_response_code(response_code: $this->responseCode->value);

        foreach($this->headers as $name => $header)
            header(header: "$name: $header");

        $this->render();
    }
}