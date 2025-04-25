<?php

namespace SaboCore\Core\Global;

/**
 * Framework configuration
 */
class FrameworkConfiguration
{
    /**
     * @var string Generic parameters regex
     */
    protected string $genericParamsRegex;


    /**
     * @return string Generic parameters regex
     */
    public function getGenericParamsRegex(): string
    {
        return $this->genericParamsRegex;
    }

    /**
     * Modify the generic params regex
     * @param string $genericParamsRegex Generic param regex
     * @return $this
     */
    public function setGenericParamsRegex(string $genericParamsRegex): static
    {
        $this->genericParamsRegex = $genericParamsRegex;
        return $this;
    }
}