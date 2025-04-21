<?php

namespace SaboCore\Core\Definitions;

/**
 * Scanner
 */
interface Scanner
{
    /**
     * Scan the content of the provided element
     * @param mixed $toScan Element to can
     * @return mixed Element content
     */
    public function scan(mixed $toScan):mixed;
}