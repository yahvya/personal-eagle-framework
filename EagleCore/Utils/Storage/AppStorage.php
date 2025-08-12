<?php

namespace Yahvya\EagleFramework\Utils\Storage;

use Yahvya\EagleFramework\Config\FrameworkConfig;
use Yahvya\EagleFramework\Routing\Application\Application;
use Throwable;

/**
 * @brief Application storage manager
 */
abstract class AppStorage
{
    /**
     * @brief Perform a copy of the provided file in the destination
     * @param string $storagePath Path of the file with the path as the root of the path
     * @param string $fileBasePath Absolute path of the file to copy
     * @param bool $createFoldersIfNotExists If true, it will generate all the missing directories included in the destination path
     * @return bool Action success state
     */
    public static function storeClassicFile(string $storagePath, string $fileBasePath, bool $createFoldersIfNotExists = true): bool
    {
        try
        {
            $storagePath = self::buildStorageCompletePath(pathFromStorage: $storagePath);
            $dirname = @dirname(path: $storagePath);

            if ($createFoldersIfNotExists && !is_dir(filename: $dirname))
            {
                if (!@mkdir(directory: $dirname, recursive: true))
                    return false;
            }

            return @copy(from: $fileBasePath, to: $storagePath);
        }
        catch (Throwable)
        {
            return false;
        }
    }

    /**
     * @brief Store the content
     * @param string $storagePath Path of the file with the path as the root of the path
     * @param string $content Content to store
     * @param bool $createFoldersIfNotExists If true, it will generate all the missing directories included in the destination path
     * @return bool Action success state
     */
    public static function storeContent(string $storagePath, string $content, bool $createFoldersIfNotExists = true): bool
    {
        try
        {
            $storagePath = self::buildStorageCompletePath(pathFromStorage: $storagePath);
            $dirname = @dirname(path: $storagePath);

            if ($createFoldersIfNotExists && !is_dir($dirname))
            {
                if (!@mkdir(directory: $dirname, recursive: true))
                    return false;
            }

            return @file_put_contents(filename: $storagePath, data: $content) !== false;
        }
        catch (Throwable)
        {
            return false;
        }
    }

    /**
     * @brief Upload the form file in the storage
     * @param string $storagePath Path of the file with the path as the root of the path
     * @param string $fileTmpName $_FILE tmp_name associated with the file
     * @param bool $createFoldersIfNotExists If true, it will generate all the missing directories included in the destination path
     * @return bool Action success state
     */
    public static function storeFormFile(string $storagePath, string $fileTmpName, bool $createFoldersIfNotExists = true): bool
    {
        try
        {
            $storagePath = self::buildStorageCompletePath(pathFromStorage: $storagePath);
            $dirname = @dirname($storagePath);

            if ($createFoldersIfNotExists && !is_dir(filename: $dirname))
            {
                if (!@mkdir(directory: $dirname, recursive: true))
                    return false;
            }

            return @move_uploaded_file(from: $fileTmpName, to: $storagePath);
        }
        catch (Throwable)
        {
            return false;
        }
    }

    /**
     * @brief Build the absolute path by adding the application storage path
     * @param string $pathFromStorage Path of the file with the path as the root of the path
     * @return string The built absolute path
     */
    public static function buildStorageCompletePath(string $pathFromStorage): string
    {
        try
        {
            $completePath = APP_CONFIG->getConfig(name: "ROOT") . Application::getFrameworkConfig()->getConfig(name: FrameworkConfig::STORAGE_DIR_PATH->value);

            if (str_ends_with(haystack: $completePath, needle: "/")) $completePath = substr(string: $completePath, offset: 0, length: -1);
            if (!str_starts_with(haystack: $pathFromStorage, needle: "/")) $pathFromStorage = "/$pathFromStorage";

            return $completePath . $pathFromStorage;
        }
        catch (Throwable)
        {
            return $pathFromStorage;
        }
    }
}