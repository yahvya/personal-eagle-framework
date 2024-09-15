<?php

namespace SaboCore\SaboCli\Commands\Commands;

/**
 * @brief requirement command types
 */
enum RequirementTypes:string{
    /**
     * @brief represent a command to execute
     * @require command-install-key
     */
    case COMMAND = "command";
}