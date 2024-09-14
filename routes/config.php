<?php

use SaboCore\Routing\Routes\Route;

/**
 * @brief Routes configuration
 * @attention do not remove this file
 */

# -----------------------------------------------------------------------------
# define generic params matcher
#   - the regex must capture the name of the generic parameter
#   - the generic parameter name must be compatible with php variables naming
# -----------------------------------------------------------------------------

Route::setGenericParamsMatchRegex(regex: "\:([a-zA-Z_]+)");