<?php

namespace Yahvya\EagleFramework\Utils\FileManager;

use Override;
use Yahvya\EagleFramework\Routing\Response\DownloadResponse;
use Yahvya\EagleFramework\Treatment\TreatmentException;
use Yahvya\EagleFramework\Utils\Storage\AppStorage;

/**
 * @brief Form file handler ($_FILES)
 */
class FormFileManager extends FileManager
{
    /**
     * @param array $fileDatas File data with the $_FILES format
     */
    public function __construct(protected array $fileDatas)
    {
        parent::__construct(fileAbsolutePath: $fileDatas["tmp_name"]);
    }

    /**
     * @inheritDoc
     * @attention Inactive method for form file data, use FileManager
     */
    #[Override]
    public function getToDownload(?string $fileName = null): DownloadResponse
    {
        throw new TreatmentException(message: "This file can't be download", isDisplayable: true);
    }

    #[Override]
    public function storeIn(string $path, bool $createFoldersIfNotExists = true): bool
    {
        return
            $this->getErrorState() == 0 &&
            AppStorage::storeFormFile(
                storagePath: $path,
                fileTmpName: $this->fileAbsolutePath,
                createFoldersIfNotExists: $createFoldersIfNotExists
            );
    }

    /**
     * @inheritDoc
     * @attention Inactive method for form file data, use FileManager
     */
    #[Override]
    public function getFromStorage(): ?FileContentManager
    {
        return null;
    }

    /**
     * @inheritDoc
     * @attention Inactive method for form file data, use FileManager
     */
    #[Override]
    public function delete(): bool
    {
        return false;
    }

    /**
     * @return string File mime type
     * @attention Do not rely on this method for the security of your file
     */
    public function getType(): string
    {
        return $this->fileDatas["type"] ?? "";
    }

    /**
     * @brief Check if the file mime is included in the types to check
     * @param string ...$typesToCheck Types to check
     * @return bool If included
     * @attention Do not rely on this method for the security of your file
     */
    public function isInTypes(string ...$typesToCheck): bool
    {
        return in_array(needle: $this->getType(), haystack: $typesToCheck);
    }

    /**
     * @return int File error state
     */
    public function getErrorState(): int
    {
        return $this->fileDatas["error"];
    }

    /**
     * @return int File size
     * @attention Do not rely on this method for the security of your file
     */
    public function getSize(): int
    {
        return $this->fileDatas["size"];
    }
}