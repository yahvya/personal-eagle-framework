<?php

namespace SaboCore\Config;

/**
 * @brief Configuration de maintenant
 * @author yahaya bathily https://github.com/yahvya
 */
enum MaintenanceConfig:string{
    /**
     * @brief Si le site est en état de maintenant
     * @type boolean
     */
    case IS_IN_MAINTENANCE = "isInMaintenance";

    /**
     * @brief Lien secret de connexion au site
     * @type string
     */
    case SECRET_LINK = "secretLink";

    /**
     * @brief Class de gestion d'accès au site
     * @type string
     */
    case ACCESS_MANAGER = "accessManager";
}