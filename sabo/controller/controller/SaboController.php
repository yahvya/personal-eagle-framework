<?php

namespace Sabo\Controller\Controller;

use Sabo\Config\SaboConfig;
use Sabo\Config\SaboConfigAttributes;
use Sabo\Controller\TwigExtension\SaboExtension;
use Sabo\Utils\String\RandomStringGenerator;
use Sabo\Utils\String\RandomStringType;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

/**
 * parent des controller
 */
abstract class SaboController{
    use RandomStringGenerator;

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

        $this->manageFlashDatas();
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
    protected function checkCsrf(string $postKey,bool $removeIfOk = true):bool{
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
    protected function setFlashData(string $flashKey,mixed $data,int $duration = 1):void{
        if($duration < 1) $duration = 1;

        $duration++;

        $_SESSION["sabo"]["flashDatas"][$flashKey] = [
            "data" => $data,
            "counter" => $duration
        ];
    }

    /**
     * @param flashKey la clé de la donnée flash
     * @return mixed la donnée flash ou null si elle n'existe pas
     */
    protected function getFlashData(string $flashKey):mixed{
        return !empty($_SESSION["sabo"]["flashDatas"][$flashKey]) ? $_SESSION["sabo"]["flashDatas"][$flashKey]["data"] : NULL;
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
        }

        self::$twigExtensions = $extensions;
    }
}   