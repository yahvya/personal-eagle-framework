<?php

namespace SaboCore\Routing\Response;

use Override;

/**
 * @brief html response
 */
class HtmlResponse extends Response{
    /**
     * @param string $content html response
     */
    public function __construct(string $content){
        $this->content = $content;

        $this->setHeader(name: "Content-Type",value: "text/html; charset=UTF-8");
    }

    /**
     * @brief create a html response from the given file path or null if file not found
     * @param string $filePath file path
     * @return HtmlResponse|null response or null if not found
     */
    public static function fromFile(string $filePath):?HtmlResponse{
        $fileContent =  @file_get_contents(filename: $filePath);

        if($fileContent === null)
            return null;
        
        return new HtmlResponse(content: $fileContent);
    }

    #[Override]
    public function render(): void{
        echo $this->content;
    }
}