<?php

namespace Yahvya\EagleFramework\Routing\Response;

use Override;
use Symfony\Component\Mime\MimeTypes;
use Throwable;

/**
 * @brief Resource provide response
 */
class ResourceResponse extends Response
{
    /**
     * @param string $ressourceAbsolutePath Absolute path of the file to provide
     * @attention The provided file should exist
     */
    public function __construct(string $ressourceAbsolutePath)
    {
        $this->content = $ressourceAbsolutePath;

        try
        {
            $fileExtension = @pathinfo(path: $ressourceAbsolutePath, flags: PATHINFO_EXTENSION);

            $this->setHeader(name: "Content-Type", value: (new MimeTypes)->getMimeTypes($fileExtension)[0]);
        }
        catch (Throwable)
        {
        }
    }

    #[Override]
    protected function renderContent(): never
    {
        try
        {
            @readfile(filename: $this->content);
        }
        catch (Throwable)
        {
            die("Resource not found");
        }
        die();
    }
}
