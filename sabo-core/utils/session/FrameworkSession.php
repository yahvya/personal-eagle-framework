<?php

namespace SaboCore\Utils\Session;

/**
 * @brief Clés de session du framework
 * @author yahaya bathily https://github.com/yahvya
 */
enum FrameworkSession:string{
    /**
     * @brief Stockage de l'accès autorisé durant la maintenance
     */
    case MAINTENANCE_ACCESS = "maintenanceAccess";
}