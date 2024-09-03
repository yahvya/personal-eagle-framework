<?php

/**
 * @brief Cron task initializer, include this script
 */

# --------------------------------------------------------------------
# define consts
# --------------------------------------------------------------------

/**
 * @const app root path
 * @attention without / at the end
 */

use SaboCore\Application\Application\Application;

const APP_ROOT = __DIR__ . "/..";

# --------------------------------------------------------------------
# load autoloader
# --------------------------------------------------------------------

require_once APP_ROOT . "/sabo-core/vendor/autoload.php";
require_once APP_ROOT . "/vendor/autoload.php";

# --------------------------------------------------------------------
# launch cron requirements loading
# --------------------------------------------------------------------

(new Application)->launchCron();
