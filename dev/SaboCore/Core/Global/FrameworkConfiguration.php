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
     * @var string The default regex to match a generic parameter in the request url
     */
    protected string $defaultGenericParamRegex;

    /**
     * @return string The default regex to match a generic parameter in the request url
     */
    public function getDefaultGenericParamRegex(): string
    {
        return $this->defaultGenericParamRegex;
    }

    /**
     * Modify the default regex to match a generic parameter in the request url
     * @param string $defaultGenericParamRegex Regex
     * @return $this
     */
    public function setDefaultGenericParamRegex(string $defaultGenericParamRegex): static
    {
        $this->defaultGenericParamRegex = $defaultGenericParamRegex;
        return $this;
    }

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