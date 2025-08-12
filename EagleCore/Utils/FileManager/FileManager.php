<?php

namespace Yahvya\EagleFramework\Utils\FileManager;

use Override;
use Yahvya\EagleFramework\Routing\Response\DownloadResponse;
use Yahvya\EagleFramework\Utils\Storage\AppStorage;
use Yahvya\EagleFramework\Utils\Storage\Storable;

/**
 * @brief File server manager
 */
class FileManager implements Storable
{
    /**
     * @param string $fileAbsolutePath Absolute path to the file
     */
    public function __construct(protected string $fileAbsolutePath)
    {
    }

    /**
     * @return bool Whether the file exists
     */
    public function fileExists(): bool
    {
        return @file_exists(filename: $this->fileAbsolutePath);
    }

    /**
     * @brief Finds the file extension
     * @param bool $fromFirstOccur If true, get the extension from the first '.' found (e.g. file.blade.php = blade.php), otherwise from the last '.' (e.g. file.blade.php = php)
     * @param string $extensionSeparator Extension separator, default is "."
     * @return string|null The found extension or null if none found
     */
    public function getExtension(bool $fromFirstOccur = true, string $extensionSeparator = "."): ?string
    {
        $extension = $this->fileAbsolutePath;

        // Extract extension as long as the result contains path separators
        do
        {
            $pos = $fromFirstOccur ? @strpos(haystack: $extension, needle: $extensionSeparator) : @strrpos(haystack: $extension, needle: $extensionSeparator);

            if ($pos === false) return null;

            $extension = @substr(string: $extension, offset: $pos + 1);
        } while (@str_contains(haystack: $extension, needle: "/") || str_contains(haystack: $extension, needle: "\\"));

        return $extension;
    }

    /**
     * @param string|null $fileName Name to give to the downloaded file, or null to keep default
     * @return DownloadResponse File ready for download
     */
    public function getToDownload(?string $fileName = null): DownloadResponse
    {
        return new DownloadResponse(ressourceAbsolutePath: $this->fileAbsolutePath, chosenName: $fileName);
    }

    /**
     * @return string The full path of the file
     */
    public function getPath(): string
    {
        return $this->fileAbsolutePath;
    }

    /**
     * @brief Stores the file in the storage folder (keeping the current file)
     * @param string $path Path relative to the storage root folder (/)
     * @param bool $createFoldersIfNotExists If true, creates missing folders in the new path
     * @return bool Whether the storage was successful
     */
    #[Override]
    public function storeIn(string $path, bool $createFoldersIfNotExists = true): bool
    {
        return AppStorage::storeClassicFile(
            storagePath: $path,
            fileBasePath: $this->fileAbsolutePath,
            createFoldersIfNotExists: $createFoldersIfNotExists
        );
    }

    /**
     * @brief Deletes the file
     * @return bool Whether the deletion was successful
     */
    public function delete(): bool
    {
        return @unlink(filename: $this->fileAbsolutePath);
    }

    /**
     * @return FileContentManager|null File content manager if file read succeeds, null otherwise
     * @attention Suitable for text content files only
     */
    #[Override]
    public function getFromStorage(): ?FileContentManager
    {
        $fileContent = @file_get_contents(filename: $this->fileAbsolutePath);

        return $fileContent === false ? null : new FileContentManager(fileContent: $fileContent);
    }
}
