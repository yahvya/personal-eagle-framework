<?php

namespace Yahvya\EagleFramework\Utils\FileManager;

use Yahvya\EagleFramework\Treatment\TreatmentException;

/**
 * @brief File content manager
 */
class FileContentManager
{
    /**
     * @var string File content
     */
    protected string $fileContent;

    /**
     * @param string $fileContent Associated content
     */
    public function __construct(string $fileContent)
    {
        $this->fileContent = $fileContent;
    }

    public function getContent(): string
    {
        return $this->fileContent;
    }

    /**
     * @return array Decoded JSON file content
     * @throws TreatmentException If the file can't be converted
     */
    public function getJsonContent(): array
    {
        $convertedContent = @json_decode(json: $this->fileContent, associative: true);

        if (gettype(value: $convertedContent) !== "array")
            throw new TreatmentException(message: "The file can't be convert as a JSON", isDisplayable: false);

        return $convertedContent;
    }
}