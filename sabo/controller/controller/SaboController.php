<?php

namespace Sabo\Controller\Controller;

use Sabo\Config\EnvConfig;
use Sabo\Config\SaboConfig;
use Sabo\Config\SaboConfigAttributes;
use Sabo\Controller\TwigExtension\SaboExtension;
use Sabo\Controller\TwigExtension\SaboRouteExtension;
use Sabo\Middleware\Exception\MiddlewareException;
use Sabo\Utils\String\RandomStringGenerator;
use Sabo\Utils\String\RandomStringType;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

/**
 * parent des controller
 */
abstract class SaboController{
    use RandomStringGenerator;

    public static array $twigExtensions;

    private static SaboRouteExtension $routeExtension;

    protected Environment $twig;

    public function __construct(){
        $this->pseudoConstruct();

        $this->manageFlashDatas();
    }   

    /**
     * affiche la page twig
     * @param viewFilePath chemin du fichier template
     * @param viewParams tableau de données à envoyé à la vue
     */
    protected function render(string $viewFilePath,array $viewParams = []):never{
        http_response_code(200);

        $folder = ROOT . SaboConfig::getStrConfig(SaboConfigAttributes::VIEWS_FOLDER_PATH);

        $loader = new FilesystemLoader($folder);

        $this->twig = new Environment($loader,[
            "debug" => SaboConfig::getBoolConfig(SaboConfigAttributes::DEBUG_MODE)
        ]);
        
        // ajout des extension
        foreach(self::$twigExtensions as $twigExtension){
            $twigExtension->setCurrentFile($folder . $viewFilePath);

            $this->twig->addExtension($twigExtension);
        }  

        die($this->twig->render($viewFilePath,array_merge($viewParams,EnvConfig::getViewEnv() ) ) );
    }

    /**
     * affiche un rendu json
     * @param data les données à affiché
     */
    protected function renderJson(array $data):never{
        header("Content-Type: application/json; charset=utf-8");
        
        die(json_encode($data) );
    }

    /**
     * à redéfinir en temps que constructeur intermédiaire
     */
    protected function pseudoConstruct():void{}

    /**
     * génère un token csrf
     * @return string le token généré
     */
    protected function generateCsrf():string{
        $token = self::generateString(17,false,RandomStringType::SPECIALCHARS);

        do
            $key = self::generateString(25,false,RandomStringType::SPECIALCHARS);
        while($this->getFlashData($key) != null);

        $this->setFlashData($key,$token);

        return implode("#",[$token,$key]);
    }

    /**
     * vérifie le token csrf 
     * @param postKey clé du tableau $_POST
     * @return bool si le token est valide ou non
     */
    protected function checkCsrf(string $postKey):bool{
        if(!empty($_POST[$postKey]) && gettype($_POST[$postKey]) == "string"){
            $tokenData = explode("#",$_POST[$postKey]);

            if(count($tokenData) == 2){
                list($token,$key) = $tokenData;

                $storedToken = $this->getFlashData($key);

                if(gettype($storedToken) == "string" && strcmp($token,$storedToken) == 0) return true;
            }
        }
        
        return false;
    }

    /**
     * défini un donnée flash
     * @param flashKey la clé de la donnée flash
     * @param data la donnée à insérer
     * @param duration le nombre de rafraichissement autorisé (min 1)
     */
    protected function setFlashData(string $flashKey,mixed $data,int $duration = 1):SaboController{
        if($duration < 1) $duration = 1;

        $duration++;

        $_SESSION["sabo"]["flashDatas"][$flashKey] = [
            "data" => $data,
            "counter" => $duration
        ];

        return $this;
    }

    /**
     * @param flashKey la clé de la donnée flash
     * @return mixed la donnée flash ou null si elle n'existe pas
     */
    protected function getFlashData(string $flashKey):mixed{
        return !empty($_SESSION["sabo"]["flashDatas"][$flashKey]) ? $_SESSION["sabo"]["flashDatas"][$flashKey]["data"] : NULL;
    }

    /**
     * @param e l'exception
     * @param replaceMessage le message en cas d'erreur non affichable
     * @return string le message d'erreur affichable
     */
    protected function getErrorMessageFrom(MiddlewareException $e,string $replaceMessage = "Une erreur technique s'est produite"){
        return $e->getIsDisplayable() ? $e->getMessage() : $replaceMessage;
    }

    /**
     * @param key la clé post
     * @return mixed|null la donnée si elle est trouvée ou null
     */
    protected function getValueOrNull(string $key):mixed{  
        return !empty($_POST[$key]) ? $_POST[$key] : null;
    }

    /**
     * gère les données flash à supprimer
     */
    private function manageFlashDatas():void{
        if(!empty($_SESSION["sabo"]["flashDatas"]) ){
            foreach($_SESSION["sabo"]["flashDatas"] as $key => $flashData){
                $flashData["counter"]--;

                if($flashData["counter"] == 0) 
                    unset($_SESSION["sabo"]["flashDatas"][$key]);
                else
                    $_SESSION["sabo"]["flashDatas"][$key] = $flashData;
            }   
        }
    }

    /**
     * initie les ressources des controllers
     */
    public static function initControllers():void{
        // intitiations des extensions twig
        $extensions = array_merge(SaboConfig::getUserExtensions(),SaboExtension::getExtensions() );
        
        foreach($extensions as $key => $extensionClass){
            call_user_func([$extensionClass,"initExtension"]);

            $extensions[$key] = new $extensionClass();

            // récupération de l'extension de route
            if($extensionClass == SaboRouteExtension::class) self::$routeExtension = $extensions[$key];
        }

        self::$twigExtensions = $extensions;
    }

    /**
     * redirige sur le lien lié au nom de route donné, si non debug et route inexistante alors page d'accueil par défaut
     * @param routeName nom de la route
     * @param routeParams paramètres génériques du lien à remplacer
     * @throws Exception en mode debug si la route n'existe pas
     */
    public static function redirectToRoute(string $routeName,array $routeParams = []):never{
        self::redirectToLink(self::$routeExtension->getRoute($routeName,$routeParams) );
    }   

    /**
     * redirige sur le lien donné
     * @param link le lien
     */
    public static function redirectToLink(string $link):never{
        header("Location: {$link}");

        die();
    }

    /**
     * @return SaboRouteExtension instance de l'extension des routes
     */
    public static function getRouteExtension():SaboRouteExtension{
        return self::$routeExtension;
    }
}   