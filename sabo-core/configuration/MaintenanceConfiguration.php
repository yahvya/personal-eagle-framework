<?php

namespace SaboCore\Configuration;

/**
 * @brief maintenance configuration
 */
readonly abstract class MaintenanceConfiguration{
    /**
     * @const application maintenance state, True for yes, False for no
     * @required
     */
    public const string IS_IN_MAINTENANCE = "IS_IN_MAINTENANCE";

    /**
     * @const secret access link , this link have to be entered in the navigator to have the access to the application during maintenance. Add a GET parameter named "code" to specify the access code. The code hash's will be stored in the storage maintenance directory
     * @required
     */
    public const string SECRET_ACCESS_LINK = "SECRET_ACCESS_LINK";

    /**
     * @brief access code hash
     */
    public const string ACCESS_CODE = "ACCESS_CODE";

    /**
     * @brief count of try before blocking access
     */
    public const string MAX_TRY = "MAX_TRY";
}