<?php

namespace SaboCore\Controller;

use SaboCore\Config\ConfigException;
use SaboCore\Routing\Request\Request;
use SaboCore\Routing\Response\Response;

/**
 * @brief gestion de la maintenance
 * @author yahaya bathily https://github.com/yahvya
 */
abstract class MaintenanceController extends Controller{
    public function __construct(){
        parent::__construct();
    }

    /**
     * @brief Affiche la page d'authentification
     * @param string $secretLink lien secret de maintenance
     * @return Response la réponse de gestion de maintenance
     * @throws ConfigException
     */
    abstract public function showMaintenancePage(string $secretLink):Response;

    /**
     * @brief Vérifie l'accès au site
     * @param Request $request données de requête
     * @return bool si l'accès est autorisé
     */
    public abstract function verifyLogin(Request $request):bool;
}