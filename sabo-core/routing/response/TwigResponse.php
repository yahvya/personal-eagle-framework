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
            $environment = self::newEnvironment(viewsPath: [APP_CONFIG->getConfig(name: "ROOT") . "/src/views/"]);

            parent::__construct(content: $environment->render(name: $pathFromViews,context: $datas) );
        }
        catch(Throwable){
            parent::__construct(content: "Veuillez rechargez la page");
        }
    }

    /**
     * @brief Crée un environnement twig
     * @param array $viewsPath chemin d'entrée des vues
     * @return Environment|null l'environnement crée ou null
     */
    public static function newEnvironment(array $viewsPath):Environment|null{
        try{
            $loader = new FilesystemLoader(paths: $viewsPath);
            $environment = new Environment(
                loader: $loader,
                options: [
                "cache" => APP_CONFIG->getConfig(name: "ROOT") . "/sabo-core/views/twig",
                "debug" => Application::getEnvConfig()->getConfig(name: EnvConfig::DEV_MODE_CONFIG->value),

            ]);

            // enregistrement des extensions twig
            $extensions = registerTwigExtensions();

            foreach($extensions as $extension)
                $environment->addExtension(extension: (new ReflectionClass(objectOrClass: $extension))->newInstance() );

            return $environment;
        }
        catch(Throwable){
            return null;
        }
    }
}