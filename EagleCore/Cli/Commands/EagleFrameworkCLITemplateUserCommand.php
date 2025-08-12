<?php

namespace Yahvya\EagleFramework\Cli\Commands;

use Yahvya\EagleFramework\Config\ConfigException;
use Throwable;

/**
 * @brief Template file-based commands
 */
abstract class EagleFrameworkCLITemplateUserCommand extends EagleFrameworkCLICommand
{
    /**
     * @const string Templates directory's path
     */
    public const string TEMPLATES_DIR_PATH = "/storage/templates";

    /**
     * @brief Create a new file based on the template
     * @param string $templatePath Template file path from the templates storage directory path
     * @param string $dstPath Absolute destination path of the file to generate
     * @param array{string:string} $replacements Data to replace in the template with the format ["clé" → "remplacement"]
     * @return bool If the creation succeeds
     */
    protected function createFromTemplate(string $templatePath, string $dstPath, array $replacements): bool
    {
        try
        {
            $templateContent = @file_get_contents(filename: self::getCliRoot() . self::TEMPLATES_DIR_PATH . $templatePath);

            if ($templateContent === false) return false;

            foreach ($replacements as $key => $replace)
                $templateContent = str_replace(search: '{' . $key . '}', replace: $replace, subject: $templateContent);

            return @file_put_contents(filename: $dstPath, data: $templateContent) !== false;
        }
        catch (Throwable)
        {
            return false;
        }
    }

    /**
     * @return string Root path of the CLI directory
     * @throws ConfigException On error
     */
    public static function getCliRoot(): string
    {
        return APP_CONFIG->getConfig(name: "ROOT") . "/EagleCore/Cli";
    }

    /**
     * @brief Format the provided string to the class format
     * @param string $baseName Initial name string
     * @return string Formated name
     */
    public static function formatNameForClass(string $baseName): string
    {
        return implode(
            array: array_map(
                callback: fn(string $part): string => ucfirst(string: strtolower(string: $part)),
                array: explode(separator: " ", string: $baseName)
            )
        );
    }

    /**
     * @brief Search the namespace and the directory of a psr-4 class recursively from the provided folder path
     * @param string $className Class name
     * @param string $from Initial search directory path
     * @return array{string:string}|null Null if not found or the data in format ["namespace" → "..." ou null si non trouvé,"directory" → "..."]
     */
    public static function findClassDatas(string $className, string $from): array|null
    {
        $dirContent = @scandir(directory: $from);

        if ($dirContent === false)
            $dirContent = [];

        $dirContent = array_diff($dirContent, [".", ".."]);
        $fileKey = array_search(needle: "$className.php", haystack: $dirContent);

        if (is_int(value: $fileKey))
        {
            $fileContent = @file_get_contents(filename: "$from/$className.php");

            if ($fileContent === false)
                return null;

            @preg_match(pattern: "#namespace (.*);#", subject: $fileContent, matches: $matches);

            return [
                "namespace" => $matches[1] ?? null,
                "directory" => $from
            ];
        }

        foreach ($dirContent as $contentName)
        {
            $contentAbsolutePath = "$from/$contentName";

            if (!is_dir(filename: $contentAbsolutePath))
                continue;

            $result = self::findClassDatas(className: $className, from: $contentAbsolutePath);

            if ($result !== null)
                return $result;
        }

        return null;
    }
}