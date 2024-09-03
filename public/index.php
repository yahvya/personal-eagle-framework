<?php

/**
 * @brief Sabo framework entrypoint
 * @author yahaya https://github.com/yahvya
 */

# --------------------------------------------------------------------
# define consts
# --------------------------------------------------------------------

/**
 * @const app root path
 * @attention without / at the end
 */
const APP_ROOT = __DIR__ . "/..";

# --------------------------------------------------------------------
# loading autoloader
# --------------------------------------------------------------------

require_once APP_ROOT . "/sabo-core/vendor/autoload.php";
require_once APP_ROOT . "/vendor/autoload.php";

# --------------------------------------------------------------------
# launch app
# --------------------------------------------------------------------