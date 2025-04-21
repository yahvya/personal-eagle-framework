<?php

namespace SaboCore\Core\Scanners;

use SaboCore\Core\Definitions\Scanner;
use TypeError;

/**
 * Directory function scanner
 */
class DirectoryFunctionScanner implements Scanner
{
    /**
     * Scan the provided directory to find each file which contain a function
     * @param string $toScan Absolute path to scan
     * @return string[] List of files absolute path which contain functions
     * @throws TypeError On error
     */
    public function scan(mixed $toScan): array
    {
        if(!@is_dir(filename: $toScan))
            return [];

        // format path
        if(!str_ends_with(haystack: $toScan,needle: '/') && !str_ends_with(haystack: $toScan,needle: '\\'))
            $toScan = "$toScan/";

        return $this->recursiveScan(directoryPath: $toScan);
    }

    /**
     * Scan the provided directory to find each file which contain a function
     * @param string $directoryPath Absolute path to scan
     * @return string[] List of files absolute path which contain functions
     * @throws TypeError On error
     */
    protected function recursiveScan(string $directoryPath): array
    {
        $result = [];

        $directoryContent = array_diff(@scandir(directory: $directoryPath),['.','..']);

        foreach($directoryContent as $file)
        {
            $fileAbsolutePath = $directoryPath . $file;

            if(@is_dir(filename: $fileAbsolutePath))
            {
                $result = array_merge($result, $this->recursiveScan(directoryPath: "$fileAbsolutePath/"));
                continue;
            }

            $fileContent = @file_get_contents(filename: $fileAbsolutePath);

            if(@preg_match(pattern: "#function [^(]+\(#",subject: $fileContent))
               $result[] = $fileAbsolutePath;
        }

        return $result;
    }
}