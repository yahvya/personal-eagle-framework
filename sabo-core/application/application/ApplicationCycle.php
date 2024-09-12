<?php

namespace SaboCore\Application\Application;

/**
 * @brief application life cycle
 * @attention the associated numbers are ordered
 */
enum ApplicationCycle:int{
    /**
     * @brief error during cycle
     * @param mixed $error the error as Exception
     */
    case ERROR_IN_CYCLE = 0;

    /**
     * @brief on hooks loading
     */
    case INIT = 1;

    /**
     * @brief after all configuration loaded
     */
    case CONFIG_LOADED = 2;

    /**
     * @brief before database initializing
     */
    case BEFORE_DATABASE_INIT = 3;

    /**
     * @brief after database initializing
     */
    case AFTER_DATABASE_INIT = 4;

    /**
     * @brief routing launching
     */
    case START_ROUTING = 5;

    /**
     * @brief maintenance management
     */
    case CHECK_MAINTENANCE = 6;

    /**
     * @brief on maintenance access blocked
     */
    case MAINTENANCE_BLOCK = 7;

    /**
     * @brief route founded, with the search results
     * @param array $searchResults format ["route" => Route,"match" => MatchResult]
     */
    case ROUTE_FOUNDED = 8;

    /**
     * @brief route access condition fail
     * @param Callable $conditionCallable failed callable
     */
    case ROUTE_VERIFIER_FAILED = 9;

    /**
     * @brief Route not found
     */
    case ROUTE_NOT_FOUND = 10;

    /**
     * @brief response rendering (Callable, mixed $args), the controller or the given function as callabe and an array of the provided args
     */
    case RENDER_RESPONSE = 11;
}
