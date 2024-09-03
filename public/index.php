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
# load autoloader
# --------------------------------------------------------------------

require_once APP_ROOT . "/sabo-core/vendor/autoload.php";
require_once APP_ROOT . "/vendor/autoload.php";

# --------------------------------------------------------------------
# load custom scripts
# --------------------------------------------------------------------
require_once APP_ROOT . "/sabo-core/custom-scripts/global-functions.php";

# --------------------------------------------------------------------
# launch app
# --------------------------------------------------------------------