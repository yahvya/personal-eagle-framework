<?php

use SaboCore\Routing\Routes\Route;

/**
 * @brief Routes configuration
 * @attention do not remove this file
 */

# --------------------------------------------------------------------
# define generic params matcher
# --------------------------------------------------------------------

Route::setGenericParamsMatchRegex(regex: "\:([a-zA-Z_]+)");