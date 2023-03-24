<?php

namespace Sabo\Controller\Controller;

use Sabo\Config\SaboConfig;
use Sabo\Config\SaboConfigAttributes;
use Sabo\Controller\TwigExtension\SaboExtension;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

/**
 * parent des controller
 */
abstract class SaboController{
    private static array $twigExtensions;

    protected Environment $twig;

    public function __construct(){
        $this->pseudoConstruct();

        $loader = new FilesystemLoader(ROOT . SaboConfig::getStrConfig(SaboConfigAttributes::VIEWS_FOLDER_PATH) );

        $this->twig = new Environment($loader,[
            "debug" => SaboConfig::getBoolConfig(SaboConfigAttributes::DEBUG_MODE)
        ]);

        // ajout des extension
        foreach(self::$twigExtensions as $twigExtension) $this->twig->addExtension($twigExtension);
    }   

    /**
     * @param viewFilePath chemin du fichier template
     * @param viewParams tableau de données à envoyé à la vue
     */
    protected function render(string $viewFilePath,array $viewParams = []):never{
        die($this->twig->render($viewFilePath,$viewParams) );
    }

    /**
     * à redéfinir en temps que constructeur intermédiaire
     */
    protected function pseudoConstruct():void{}

    /**
     * initie les ressources des controllers
     */
    public static function initControllers():void{
        // intitiations des extensions twig
        $extensions = array_merge(SaboConfig::getUserExtensions(),SaboExtension::getExtensions() );
        
        foreach($extensions as $key => $extensionClass){
            call_user_func([$extensionClass,"initExtension"]);

            $extensions[$key] = new $extensionClass();
        }

        self::$twigExtensions = $extensions;
    }
}   