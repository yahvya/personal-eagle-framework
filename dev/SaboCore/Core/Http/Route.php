<?php

namespace SaboCore\Core\Http;

use Closure;

/**
 * A route configuration data contract
 */
class Route
{
    /**
     * @var string Route generic link
     */
    protected string $link;

    /**
     * @var string Route name
     */
    protected string $routeName;

    /**
     * @var array{string,string} Generic params regex override
     */
    protected array $genericParamsRegexOverride = [];

    /**
     * @var (callable|Closure)[] Access verifiers as boolean callables
     */
    protected array $accessConditions = [];

    /**
     * @var array{string,callable|Closure} Request methods
     */
    protected array $requestMethods = [];

    /**
     * @param RouteManager $routeManager Route manager
     */
    public function __construct(
        public readonly RouteManager $routeManager
    )
    {
        $this->requestMethods['OPTIONS'] = function(){
          /**
           * @todo complete options request treatment
           */
        };
    }

    /**
     * @return (callable|Closure)[] The route access conditions as boolean callables
     */
    public function getAccessConditions(): array
    {
        return $this->accessConditions;
    }

    /**
     * Modify the entire array of the access conditions
     * @param (callable|Closure)[] $accessConditions Route access conditions as boolean callables
     * @return $this
     */
    public function setAccessConditions(array $accessConditions): static
    {
        $this->accessConditions = $accessConditions;

        return $this;
    }

    /**
     * @return string Generic link
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * Modify the generic link
     * @param string $link Link
     * @return $this
     */
    public function setLink(string $link): static
    {
        $this->link = $link;
        return $this;
    }

    /**
     * @return string Route name
     */
    public function getRouteName(): string
    {
        return $this->routeName;
    }

    /**
     * Modify the route name
     * @param string $routeName New route name
     * @return $this
     */
    public function setRouteName(string $routeName): static
    {
        $this->routeName = $routeName;
        return $this;
    }

    /**
     * @return array
     */
    public function getGenericParamsRegexOverride(): array
    {
        return $this->genericParamsRegexOverride;
    }

    /**
     * Modify the generic params regex
     * @param array $genericParamsRegexOverride Regex map
     * @return $this
     */
    public function setGenericParamsRegexOverride(array $genericParamsRegexOverride): static
    {
        $this->genericParamsRegexOverride = array_replace($this->genericParamsRegexOverride,$genericParamsRegexOverride);
        return $this;
    }

    /**
     * @return string[] Request methods
     */
    public function getRequestMethods(): array
    {
        return $this->requestMethods;
    }

    /**
     * Add post method to the allowed http request method
     * @param callable|Closure $handler method handler
     * @return $this
     */
    public function allowPost(array|Closure $handler):static
    {
        $this->requestMethods['POST'] = $handler;
        return $this;
    }

    /**
     * Add get method to the allowed http request method
     * @param callable|Closure $handler method handler
     * @return $this
     */
    public function allowGet(callable|Closure $handler): static
    {
        $this->requestMethods['GET'] = $handler;
        return $this;
    }

    /**
     * Add put method to the allowed http request method
     * @param callable|Closure $handler method handler
     * @return $this
     */
    public function allowPut(callable|Closure $handler): static
    {
        $this->requestMethods['PUT'] = $handler;
        return $this;
    }

    /**
     * Add patch method to the allowed http request method
     * @param callable|Closure $handler method handler
     * @return $this
     */
    public function allowPatch(callable|Closure $handler): static
    {
        $this->requestMethods['PATCH'] = $handler;
        return $this;
    }

    /**
     * Add delete method to the allowed http request method
     * @param callable|Closure $handler method handler
     * @return $this
     */
    public function allowDelete(callable|Closure $handler): static
    {
        $this->requestMethods['DELETE'] = $handler;
        return $this;
    }

    /**
     * Add head method to the allowed http request method
     * @param callable|Closure $handler method handler
     * @return $this
     */
    public function allowHead(callable|Closure $handler): static
    {
        $this->requestMethods['HEAD'] = $handler;
        return $this;
    }

    /**
     * Override the automatic options method
     * @param callable|Closure $handler method handler
     * @return $this
     */
    public function allowOptions(callable|Closure $handler): static
    {
        $this->requestMethods['OPTIONS'] = $handler;
        return $this;
    }
}