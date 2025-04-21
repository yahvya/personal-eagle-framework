<?php session_start();

// SABO FRAMEWORK ENTRYPOINT

/**
 * Application root path
 */
const APPLICATION_ROOT = __DIR__ . "/..";

require_once APPLICATION_ROOT . "/vendor/autoload.php";

use SaboCore\Core\Global\Application;

new Application()->init();