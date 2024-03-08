<?php

namespace SaboCore\Routing\Application;

use SaboCore\Config\ConfigException;
use SaboCore\Config\FrameworkConfig;
use SaboCore\Routing\Response\Response;
use SaboCore\Routing\Response\RessourceResponse;
use Throwable;

/**
 * @brief Gestionnaire du routing de l'application
 */
class RoutingManager{
    /**
     * @var string lien fourni
     */
    protected string $link;

    public function __construct(){
        $this->link = parse_url($_SERVER["REQUEST_URI"],PHP_URL_PATH);
    }

    /**
     * @brief Lance le routing de l'application
     * @return Response la réponse à afficher
     * @throws ConfigException|Throwable en cas d'erreur
     */
    public function start():Response{
        // chargement des routes
        require_once(APP_CONFIG->getConfig("ROOT") . Application::getFrameworkConfig()->getConfig(FrameworkConfig::ROUTES_BASEDIR_PATH->value) . "/routes.php");

        // vérification de maintenance


        // vérification d'accès à une ressource
        if($this->isAccessibleRessource() ) return new RessourceResponse(APP_CONFIG->getConfig("ROOT") . $this->link);

        die();
    }

    /**
     * @brief Vérifie si le lien est celui d'une ressource autorisée à l'accès par lien
     * @return bool si le lien est celui d'une ressource autorisée à l'accès par lien
     * @throws ConfigException
     */
    protected function isAccessibleRessource():bool{
        $frameworkConfig = Application::getFrameworkConfig();

        return
            // on vérifie si le chemin se trouve dans le dossier public, ou est une extension autorisée
            (
                str_starts_with($this->link,$frameworkConfig->getConfig(FrameworkConfig::PUBLIC_DIR_PATH->value)) ||
                !empty(
                    array_filter(
                        $frameworkConfig->getConfig(FrameworkConfig::AUTHORIZED_EXTENSIONS_AS_PUBLIC->value),
                        fn(string $extension):bool => str_ends_with($this->link,$extension)
                    )
                )
            ) &&
            // on vérifie que le fichier existe
            file_exists(APP_CONFIG->getConfig("ROOT") . $this->link);
    }
}