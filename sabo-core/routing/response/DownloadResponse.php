<?php

namespace SaboCore\Routing\Response;

use Override;
use Throwable;

/**
 * @brief download response
 * @author yahaya bathily https://github.com/yahvya
 */
class DownloadResponse extends Response{
    /**
     * @param string $resourceAbsolutePath file absolute path
     * @param string|null $chosenName downloadable resource name , if null the default one will be used
     * @attention the given file must exist
     */
    public function __construct(string $resourceAbsolutePath, ?string $chosenName = null){
        $this->content = $resourceAbsolutePath;

        if(@file_exists(filename: $resourceAbsolutePath) ){
            $this
                ->setHeader(name: "Content-Description",value: "File Transfer")
                ->setHeader(name: "Content-Type",value: "application/octet-stream")
                ->setHeader(name: "Content-Disposition",value:  "attachment; filename=" . ($chosenName ?? basename(path: $resourceAbsolutePath) ) )
                ->setHeader(name: "Expires",value: "0")
                ->setHeader(name: "Cache-Control",value: "must-revalidate")
                ->setHeader(name: "Pragma",value: "public");

            // resource file size
            $fileSize = @filesize(filename: $resourceAbsolutePath);

            if($fileSize !== false)
                $this->setHeader(name: "Content-Length",value: $fileSize);
        }
    }

    #[Override]
    public function render():never{
        try{
            @readfile(filename: $this->content);
        }
        catch(Throwable){
            die("Resource not found");
        }

        die();
    }
}