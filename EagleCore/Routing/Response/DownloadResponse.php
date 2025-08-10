<?php

namespace Yahvya\EagleFramework\Routing\Response;

use Override;
use Throwable;

/**
 * @brief Download response
 */
class DownloadResponse extends Response
{
    /**
     * @param string $ressourceAbsolutePath Absolute path to the file to be provided
     * @param string|null $chosenName Name to give to the downloaded file, if null the default filename is used
     * @attention The provided file must exist
     */
    public function __construct(string $ressourceAbsolutePath, ?string $chosenName = null)
    {
        $this->content = $ressourceAbsolutePath;

        if (@file_exists(filename: $ressourceAbsolutePath))
        {
            $this
                ->setHeader(name: "Content-Description", value: "File Transfer")
                ->setHeader(name: "Content-Type", value: "application/octet-stream")
                ->setHeader(name: "Content-Disposition", value: "attachment; filename=" . ($chosenName ?? basename(path: $ressourceAbsolutePath)))
                ->setHeader(name: "Expires", value: "0")
                ->setHeader(name: "Cache-Control", value: "must-revalidate")
                ->setHeader(name: "Pragma", value: "public");

            // retrieve file size
            $fileSize = @filesize($ressourceAbsolutePath);

            if ($fileSize !== false)
                $this->setHeader(name: "Content-Length", value: $fileSize);
        }
    }

    #[Override]
    protected function renderContent(): never
    {
        try
        {
            @readfile(filename: $this->content);
        } catch (Throwable)
        {
            die("Resource not found");
        }

        die();
    }
}
