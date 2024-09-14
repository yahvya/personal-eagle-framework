<?php session_start();

use SaboCore\Application\Application\Application;

# --------------------------------------------------------------------
# define consts
# --------------------------------------------------------------------

/**
 * @const app root path
 * @attention without / at the end
 */
const APP_ROOT = __DIR__ . "/..";

# --------------------------------------------------------------------
# load autoloader
# --------------------------------------------------------------------

require_once APP_ROOT . "/sabo-core/vendor/autoload.php";
require_once APP_ROOT . "/vendor/autoload.php";

# --------------------------------------------------------------------
# launch app
# --------------------------------------------------------------------

/** @noinspection PhpUnhandledExceptionInspection */
(new Application)->launchWeb();
