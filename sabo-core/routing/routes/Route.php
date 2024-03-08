<?php

namespace SaboCore\Routing\Routes;

use Closure;
use SaboCore\Config\FrameworkConfig;
use SaboCore\Routing\Application\Application;
use Throwable;

/**
 * @brief Route de l'application
 * @author yahaya bathily https://github.com/yahvya
 */
class Route{
    /**
     * @var string méthode de requête (get, post, ...)
     */
    protected string $requestMethod;

    /**
     * @var string lien
     */
    protected string $link;

    /**
     * @var string lien sous forme d'expréssion régulière de comparaison
     */
    protected string $verificationRegex;

    /**
     * @var string nom de la route
     */
    protected string $routeName;

    /**
     * @var array expressions régulières associées aux paramètres génériques
     */
    protected array $genericParamsRegex;

    /**
     * @var array ordre des paramètres génériques dans la requête [ordre → nom]
     */
    protected array $genericParamsOrder = [];

    /**
     * @var AccessVerifier[] vérificateurs d'accès à la route
     */
    protected array $accessVerifiers;

    /**
     * @var Closure|array à exécuter pour traiter la route
     */
    protected Closure|array $toExecute;

    /**
     * @param string $requestMethod méthode de requête (get, post, ...)
     * @param string $link lien
     * @param Closure|array $toExecute à exécuter pour traiter la route
     * @param string $routeName nom de la route
     * @param array $genericParamsRegex expressions régulières associées aux paramètres génériques
     * @param AccessVerifier[] $accessVerifiers vérificateurs d'accès à la route, seuls les fonctions failures sont prises en compte et retournent Response
     */
    public function __construct(string $requestMethod,string $link,Closure|array $toExecute,string $routeName,array $genericParamsRegex = [],array $accessVerifiers = []){
        // formatage du lien
        if(!str_starts_with($link,"/") ) $link = "/$link";
        if(!str_ends_with($link,"/") ) $link = "$link/";

        $this->requestMethod = $requestMethod;
        $this->link = $link;
        $this->toExecute = $toExecute;
        $this->routeName = $routeName;
        $this->genericParamsRegex = $genericParamsRegex;
        $this->accessVerifiers = $accessVerifiers;

        $this->updateConfig();
    }

    /**
     * @brief Ajoute un préfix au lien
     * @param string $prefix préfixe à ajouter à la route
     * @param array $genericParameters paramètres génériques à ajouter à la route
     * @param AccessVerifier[] $accessVerifiers vérificateurs d'accès à la route, seuls les fonctions failures sont prises en compte et retournent Response
     * @return $this
     */
    public function addPrefix(string $prefix,array $genericParameters = [],array $accessVerifiers = []):Route{
        // formatage du préfixe
        if(!str_starts_with($prefix,"/") ) $prefix = "/$prefix";
        if(str_ends_with($prefix,"/") ) $prefix = substr($prefix,0,-1);

        // combinaison lien préfixe, vérificateurs et regex
        $this->link = $prefix . $this->link;
        $this->genericParamsRegex = array_merge($this->genericParamsRegex,$genericParameters);
        $this->accessVerifiers = array_merge($this->accessVerifiers,$accessVerifiers);

        return $this->updateConfig();
    }

    /**
     * @brief Vérifie si la route match avec l'URL
     * @param string $url l'URL
     * @return MatchResult le résultat du match contenant l'association si match
     */
    public function matchWith(string $url):MatchResult{
        @preg_match("#^$this->verificationRegex$#",$url,$matches);

        if(empty($matches) ) return new MatchResult(false);

        // association des paramètres récupérés avec leur ordre
        $matchTable = [];

        unset($matches[0]);

        foreach($matches as $key => $value)
            $matchTable[$this->genericParamsOrder[$key - 1] ] = $value;

        return new MatchResult(true,$matchTable);
    }

    /**
     * @return Closure|array l'action d'exécution
     */
    public function getToExecute():Closure|array{
        return $this->toExecute;
    }

    /**
     * @return AccessVerifier[] les vérificateurs de la route
     */
    public function getAccessVerifiers():array{
        return $this->accessVerifiers;
    }

    /**
     * @return string la méthode de requête
     */
    public function getRequestMethod():string{
        return $this->requestMethod;
    }

    /**
     * @return string le nom de la route
     */
    public function getRouteName():string{
        return $this->routeName;
    }

    /**
     * @brief Met à jour les données de la route à partir des informations contenues dans le lien ainsi que les paramètres génériques
     * @return $this
     */
    protected function updateConfig():Route{
        $this->verificationRegex = str_replace("/","\/",$this->link);
        $this->genericParamsOrder = [];
        $genericParameterMatcher = ":([a-zA-Z]+)";

        try{
            $genericParameterMatcher = Application::getFrameworkConfig()->getConfig(FrameworkConfig::ROUTE_GENERIC_PARAMETER_MATCHER->value);
        }
        catch(Throwable){}

        // match des variables
        @preg_match_all("#$genericParameterMatcher#",$this->link,$matches);

        // récupération des paramètres
        foreach($matches[0] as $key => $completeMatch){
            $variableName = $matches[1][$key];

            // enregistrement dans le tableau de l'ordre
            $this->genericParamsOrder[$key] = $variableName;

            // transformation dans la chaine par regex
            $regex = $this->genericParamsRegex[$variableName] ?? ".+";
            $this->verificationRegex = str_replace($completeMatch,"($regex)",$this->verificationRegex);
        }

        $this->verificationRegex .= "?";

        return $this;
    }

    /**
     * @brief Crée une route get
     * @param string $link lien
     * @param Closure|array $toExecute à exécuter pour traiter la route
     * @param string $routeName nom de la route
     * @param array $genericParamsRegex expressions régulières associées aux paramètres génériques
     * @param AccessVerifier[] $accessVerifiers vérificateurs d'accès à la route, seuls les fonctions failures sont prises en compte et retournent Response
     * @return Route la route crée
     */
    public static function get(string $link,Closure|array $toExecute,string $routeName,array $genericParamsRegex = [],array $accessVerifiers = []):Route{
        return new Route("get",$link,$toExecute,$routeName,$genericParamsRegex,$accessVerifiers);
    }

    /**
     * @brief Crée une route DELETE
     * @param string $link Lien de la route
     * @param Closure|array $toExecute Fonction à exécuter pour traiter la route
     * @param string $routeName Nom de la route
     * @param array $genericParamsRegex Expressions régulières associées aux paramètres génériques
     * @param AccessVerifier[] $accessVerifiers Vérificateurs d'accès à la route, seuls les fonctions failures sont prises en compte et retournent Response
     * @return Route La route créée
     */
    public static function delete(string $link, Closure|array $toExecute, string $routeName, array $genericParamsRegex = [], array $accessVerifiers = []): Route {
        return new Route("delete", $link, $toExecute, $routeName, $genericParamsRegex, $accessVerifiers);
    }

    /**
     * @brief Crée une route POST
     * @param string $link Lien de la route
     * @param Closure|array $toExecute Fonction à exécuter pour traiter la route
     * @param string $routeName Nom de la route
     * @param array $genericParamsRegex Expressions régulières associées aux paramètres génériques
     * @param AccessVerifier[] $accessVerifiers Vérificateurs d'accès à la route, seuls les fonctions failures sont prises en compte et retournent Response
     * @return Route La route créée
     */
    public static function post(string $link, Closure|array $toExecute, string $routeName, array $genericParamsRegex = [], array $accessVerifiers = []): Route {
        return new Route("post", $link, $toExecute, $routeName, $genericParamsRegex, $accessVerifiers);
    }

    /**
     * @brief Crée une route PUT
     * @param string $link Lien de la route
     * @param Closure|array $toExecute Fonction à exécuter pour traiter la route
     * @param string $routeName Nom de la route
     * @param array $genericParamsRegex Expressions régulières associées aux paramètres génériques
     * @param AccessVerifier[] $accessVerifiers Vérificateurs d'accès à la route, seuls les fonctions failures sont prises en compte et retournent Response
     * @return Route La route créée
     */
    public static function put(string $link, Closure|array $toExecute, string $routeName, array $genericParamsRegex = [], array $accessVerifiers = []): Route {
        return new Route("put", $link, $toExecute, $routeName, $genericParamsRegex, $accessVerifiers);
    }

    /**
     * @brief Crée une route PATCH
     * @param string $link Lien de la route
     * @param Closure|array $toExecute Fonction à exécuter pour traiter la route
     * @param string $routeName Nom de la route
     * @param array $genericParamsRegex Expressions régulières associées aux paramètres génériques
     * @param AccessVerifier[] $accessVerifiers Vérificateurs d'accès à la route, seuls les fonctions failures sont prises en compte et retournent Response
     * @return Route La route créée
     */
    public static function patch(string $link, Closure|array $toExecute, string $routeName, array $genericParamsRegex = [], array $accessVerifiers = []): Route {
        return new Route("patch", $link, $toExecute, $routeName, $genericParamsRegex, $accessVerifiers);
    }

    /**
     * @brief Crée une route OPTIONS
     * @param string $link Lien de la route
     * @param Closure|array $toExecute Fonction à exécuter pour traiter la route
     * @param string $routeName Nom de la route
     * @param array $genericParamsRegex Expressions régulières associées aux paramètres génériques
     * @param AccessVerifier[] $accessVerifiers Vérificateurs d'accès à la route, seuls les fonctions failures sont prises en compte et retournent Response
     * @return Route La route créée
     */
    public static function options(string $link, Closure|array $toExecute, string $routeName, array $genericParamsRegex = [], array $accessVerifiers = []): Route {
        return new Route("options", $link, $toExecute, $routeName, $genericParamsRegex, $accessVerifiers);
    }

    /**
     * @brief Crée une route HEAD
     * @param string $link Lien de la route
     * @param Closure|array $toExecute Fonction à exécuter pour traiter la route
     * @param string $routeName Nom de la route
     * @param array $genericParamsRegex Expressions régulières associées aux paramètres génériques
     * @param AccessVerifier[] $accessVerifiers Vérificateurs d'accès à la route, seuls les fonctions failures sont prises en compte et retournent Response
     * @return Route La route créée
     */
    public static function head(string $link, Closure|array $toExecute, string $routeName, array $genericParamsRegex = [], array $accessVerifiers = []): Route {
        return new Route("head", $link, $toExecute, $routeName, $genericParamsRegex, $accessVerifiers);
    }

    /**
     * @brief Crée une route TRACE
     * @param string $link Lien de la route
     * @param Closure|array $toExecute Fonction à exécuter pour traiter la route
     * @param string $routeName Nom de la route
     * @param array $genericParamsRegex Expressions régulières associées aux paramètres génériques
     * @param AccessVerifier[] $accessVerifiers Vérificateurs d'accès à la route, seuls les fonctions failures sont prises en compte et retournent Response
     * @return Route La route créée
     */
    public static function trace(string $link, Closure|array $toExecute, string $routeName, array $genericParamsRegex = [], array $accessVerifiers = []): Route {
        return new Route("trace", $link, $toExecute, $routeName, $genericParamsRegex, $accessVerifiers);
    }
}