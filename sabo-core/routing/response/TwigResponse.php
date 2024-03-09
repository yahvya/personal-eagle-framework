<?php

namespace SaboCore\Routing\Response;

use ReflectionClass;
use SaboCore\Config\EnvConfig;
use SaboCore\Routing\Application\Application;
use Throwable;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * @brief Réponse twig
 * @author yahaya bathily https://github.com/yahvya
 */
class TwigResponse extends HtmlResponse{
    /**
     * @param string $pathFromViews chemin à partir du dossier des vues
     * @param array $datas données de la vue
     */
    public function __construct(string $pathFromViews,array $datas = []){
        try{
            $environment = self::newEnvironment([APP_CONFIG->getConfig("ROOT") . "/src/views/"]);

            parent::__construct($environment->render($pathFromViews,$datas) );
        }
        catch(Throwable){
            parent::__construct("Veuillez rechargez la page");
        }
    }

    /**
     * @brief Crée un environnement twig
     * @param array $viewsPath chemin d'entrée des vues
     * @return Environment|null l'environnement crée ou null
     */
    public static function newEnvironment(array $viewsPath):Environment|null{
        try{
            $loader = new FilesystemLoader($viewsPath);
            $environment = new Environment($loader,[
                "cache" => APP_CONFIG->getConfig("ROOT") . "/sabo-core/views/twig",
                "debug" => Application::getEnvConfig()->getConfig(EnvConfig::DEV_MODE_CONFIG->value),

            ]);

            // enregistrement des extensions twig
            $extensions = registerTwigExtensions();

            foreach($extensions as $extension)
                $environment->addExtension((new ReflectionClass($extension))->newInstance() );

            return $environment;
        }
        catch(Throwable){
            return null;
        }
    }
}