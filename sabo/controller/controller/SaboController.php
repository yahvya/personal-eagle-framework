<?php

namespace Sabo\Controller\Controller;

use Exception;
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

    /**
     * extensions twig
     */
    public static array $twigExtensions = [];

    /**
     * extension de gestion de routes
     */
    private static SaboRouteExtension $routeExtension;

    /**
     * environnement actuel twig
     */
    protected Environment $twig;

    /**
     * @param bool $manageFlashDatas si les données flash doivent être géré sur l'instance de ce controller
     */
    public function __construct(bool $manageFlashDatas = true){
        $this->pseudoConstruct();

        if($manageFlashDatas) $this->manageFlashDatas();
    }   

    /**
     * affiche la page twig
     * @param string $viewFilePath chemin du fichier template
     * @param array $viewParams tableau de données à envoyé à la vue
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

        try{
            die($this->twig->render($viewFilePath,array_merge($viewParams,EnvConfig::getViewEnv() ) ) );
        }
        catch(Exception $e){
            if(SaboConfig::getBoolConfig(SaboConfigAttributes::DEBUG_MODE) )
                throw $e;
            else
                call_user_func(SaboConfig::getCallableConfig(SaboConfigAttributes::TECHNICAL_ERROR_DEFAULT_PAGE) );
        }

        die();
    }

    /**
     * affiche un rendu json
     * @param array $data les données à affiché
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
     * @param int|null $expiration le délai d'expiration ou par défaut 30 min
     * @return string le token généré
     */
    protected function generateCsrf(int $expiration = 3600):string{
        $token = bin2hex(random_bytes(35) );

        do
            $key = self::generateString(25,false,RandomStringType::SPECIALCHARS);
        while($this->getFlashData($key) != null);

        $this->setFlashData($key,["token" => $token,"creationTime" => time(),"expiration" => $expiration],-1);

        return implode("#",[$token,$key]);
    }

    /**
     * vérifie le token csrf 
     * @param string $postKey clé du tableau $_POST
     * @return bool si le token est valide ou non
     */
    protected function checkCsrf(string $postKey):bool{
        if(!empty($_POST[$postKey]) && gettype($_POST[$postKey]) == "string"){
            $tokenData = explode("#",$_POST[$postKey]);

            if(count($tokenData) == 2){
                [$token,$key] = $tokenData;

                $storedTokenDatas = $this->getFlashData($key);

                if(gettype($storedTokenDatas) == "array" && strcmp($token,$storedTokenDatas["token"]) == 0) return true;
            }
        }
        
        return false;
    }

    /**
     * défini un donnée flash
     * @param string $flashKey la clé de la donnée flash
     * @param mixed $data la donnée à insérer pour rajouter un gestion d'expiration un tableau sous le format est attendu ["creationTime" => time(),"expiration" => durée_en_secondes,....]
     * @param int $duration le nombre de rafraichissement autorisé (min 1) si -1 alors la valeur est conservé jusqu'a la première lecture
     */
    protected function setFlashData(string $flashKey,mixed $data,int $duration = 1):SaboController{
        if($duration < -1 || $duration == 0) $duration = 1;

        $duration++;

        $_SESSION["sabo"]["flashDatas"][$flashKey] = [
            "data" => $data,
            "counter" => $duration,
            "untilRead" => $duration -1 == -1 
        ];

        return $this;
    }

    /**
     * @param string $flashKey la clé de la donnée flash
     * @return mixed la donnée flash ou null si elle n'existe pas
     */
    protected function getFlashData(string $flashKey):mixed{
        $data = null;

        if(!empty($_SESSION["sabo"]["flashDatas"][$flashKey]) ){
            $data = $_SESSION["sabo"]["flashDatas"][$flashKey]["data"];

            if($_SESSION["sabo"]["flashDatas"][$flashKey]["untilRead"])
                unset($_SESSION["sabo"]["flashDatas"][$flashKey]);
        }
    
        return $data;
    }

    /**
     * @param MiddlewareException $e l'exception
     * @param string $replaceMessage le message en cas d'erreur non affichable
     * @return string le message d'erreur affichable
     */
    protected function getErrorMessageFrom(MiddlewareException $e,string $replaceMessage = "Une erreur technique s'est produite"){
        return $e->getIsDisplayable() ? $e->getMessage() : $replaceMessage;
    }

    /**
     * @param string $key la clé post
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
                if($flashData["untilRead"]){

                    if(!isset($flashData["data"]["expiration"]) || !isset($flashData["data"]["creationTime"]) ) continue;

                    try{
                        // vérification de l'expiration
                        if(time() - $flashData["data"]["creationTime"] >= $flashData["data"]["expiration"]) unset($_SESSION["sabo"]["flashDatas"][$key]);

                        continue;
                    }
                    catch(Exception){}
                }

                $flashData["counter"]--;

                if($flashData["counter"] <= 0) 
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
     * @param string $routeName nom de la route
     * @param array $routeParams paramètres génériques du lien à remplacer
     * @throws Exception en mode debug si la route n'existe pas
     */
    public static function redirectToRoute(string $routeName,array $routeParams = []):never{
        self::redirectToLink(self::$routeExtension->getRoute($routeName,$routeParams) );
    }   

    /**
     * redirige sur le lien donné
     * @param string $link le lien
     */
    public static function redirectToLink(string $link):never{
        header("Location: {$link}"); die();
    }

    /**
     * @return SaboRouteExtension instance de l'extension des routes
     */
    public static function getRouteExtension():SaboRouteExtension{
        return self::$routeExtension;
    }
}   