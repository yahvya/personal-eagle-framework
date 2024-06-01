<?php

namespace SaboCore\Routing\Routes;

use Closure;
use SaboCore\Config\FrameworkConfig;
use SaboCore\Routing\Application\Application;
use SaboCore\Utils\Verification\Verifier;
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
     * @var Verifier[] vérificateurs d'accès à la route
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
     * @param Verifier[] $accessVerifiers vérificateurs d'accès à la route, seuls les fonctions failures sont prises en compte et retournent Response
     */
    public function __construct(string $requestMethod,string $link,Closure|array $toExecute,string $routeName,array $genericParamsRegex = [],array $accessVerifiers = []){
        // formatage du lien
        if(!str_starts_with(haystack: $link,needle: "/") ) $link = "/$link";
        if(!str_ends_with(haystack: $link,needle: "/") ) $link = "$link/";

        $this->requestMethod = strtolower(string: $requestMethod);
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
     * @param Verifier[] $accessVerifiers vérificateurs d'accès à la route, seuls les fonctions failures sont prises en compte et retournent Response
     * @return $this
     */
    public function addPrefix(string $prefix,array $genericParameters = [],array $accessVerifiers = []):Route{
        // formatage du préfixe
        if(!str_starts_with(haystack: $prefix,needle: "/") ) $prefix = "/$prefix";
        if(str_ends_with(haystack: $prefix,needle: "/") ) $prefix = substr(string: $prefix,offset: 0,length: -1);

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
        @preg_match(pattern: "#^$this->verificationRegex$#",subject: $url,matches: $matches);

        if(empty($matches) )
            return new MatchResult(haveMatch: false);

        // association des paramètres récupérés avec leur ordre
        $matchTable = [];

        unset($matches[0]);

        foreach($matches as $key => $value)
            $matchTable[$this->genericParamsOrder[$key - 1] ] = $value;

        return new MatchResult(haveMatch: true,matchTable: $matchTable);
    }

    /**
     * @return Closure|array l'action d'exécution
     */
    public function getToExecute():Closure|array{
        return $this->toExecute;
    }

    /**
     * @return Verifier[] les vérificateurs de la route
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
     * @return string le lien associé
     */
    public function getRouteLink():string{
        return $this->link;
    }

    /**
     * @brief Met à jour les données de la route à partir des informations contenues dans le lien ainsi que les paramètres génériques
     * @return $this
     */
    protected function updateConfig():Route{
        $this->verificationRegex = str_replace(search: "/",replace: "\/",subject: $this->link);
        $this->genericParamsOrder = [];
        $genericParameterMatcher = ":([a-zA-Z]+)";

        try{
            $genericParameterMatcher = Application::getFrameworkConfig()->getConfig(name: FrameworkConfig::ROUTE_GENERIC_PARAMETER_MATCHER->value);
        }
        catch(Throwable){}

        // match des variables
        @preg_match_all(pattern: "#$genericParameterMatcher#",subject: $this->link,matches: $matches);

        // récupération des paramètres
        foreach($matches[0] as $key => $completeMatch){
            $variableName = $matches[1][$key];

            // enregistrement dans le tableau de l'ordre
            $this->genericParamsOrder[$key] = $variableName;

            // transformation dans la chaine par regex
            $regex = $this->genericParamsRegex[$variableName] ?? "[^\/]+";
            $this->verificationRegex = str_replace(search: $completeMatch,replace: "($regex)",subject: $this->verificationRegex);
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
     * @param Verifier[] $accessVerifiers vérificateurs d'accès à la route, seuls les fonctions failures sont prises en compte et retournent Response
     * @return Route la route crée
     */
    public static function get(string $link,Closure|array $toExecute,string $routeName,array $genericParamsRegex = [],array $accessVerifiers = []):Route{
        return new Route(
            requestMethod: "get",
            link: $link,
            toExecute: $toExecute,
            routeName: $routeName,
            genericParamsRegex: $genericParamsRegex,
            accessVerifiers: $accessVerifiers
        );
    }

    /**
     * @brief Crée une route DELETE
     * @param string $link Lien de la route
     * @param Closure|array $toExecute Fonction à exécuter pour traiter la route
     * @param string $routeName Nom de la route
     * @param array $genericParamsRegex Expressions régulières associées aux paramètres génériques
     * @param Verifier[] $accessVerifiers Vérificateurs d'accès à la route, seuls les fonctions failures sont prises en compte et retournent Response
     * @return Route La route créée
     */
    public static function delete(string $link, Closure|array $toExecute, string $routeName, array $genericParamsRegex = [], array $accessVerifiers = []): Route {
        return new Route(
            requestMethod: "delete",
            link: $link,
            toExecute: $toExecute,
            routeName: $routeName,
            genericParamsRegex: $genericParamsRegex,
            accessVerifiers: $accessVerifiers
        );
    }

    /**
     * @brief Crée une route POST
     * @param string $link Lien de la route
     * @param Closure|array $toExecute Fonction à exécuter pour traiter la route
     * @param string $routeName Nom de la route
     * @param array $genericParamsRegex Expressions régulières associées aux paramètres génériques
     * @param Verifier[] $accessVerifiers Vérificateurs d'accès à la route, seuls les fonctions failures sont prises en compte et retournent Response
     * @return Route La route créée
     */
    public static function post(string $link, Closure|array $toExecute, string $routeName, array $genericParamsRegex = [], array $accessVerifiers = []): Route {
        return new Route(
            requestMethod: "post",
            link: $link,
            toExecute: $toExecute,
            routeName: $routeName,
            genericParamsRegex: $genericParamsRegex,
            accessVerifiers: $accessVerifiers
        );
    }

    /**
     * @brief Crée une route PUT
     * @param string $link Lien de la route
     * @param Closure|array $toExecute Fonction à exécuter pour traiter la route
     * @param string $routeName Nom de la route
     * @param array $genericParamsRegex Expressions régulières associées aux paramètres génériques
     * @param Verifier[] $accessVerifiers Vérificateurs d'accès à la route, seuls les fonctions failures sont prises en compte et retournent Response
     * @return Route La route créée
     */
    public static function put(string $link, Closure|array $toExecute, string $routeName, array $genericParamsRegex = [], array $accessVerifiers = []): Route {
        return new Route(
            requestMethod: "put",
            link: $link,
            toExecute: $toExecute,
            routeName: $routeName,
            genericParamsRegex: $genericParamsRegex,
            accessVerifiers: $accessVerifiers
        );
    }

    /**
     * @brief Crée une route PATCH
     * @param string $link Lien de la route
     * @param Closure|array $toExecute Fonction à exécuter pour traiter la route
     * @param string $routeName Nom de la route
     * @param array $genericParamsRegex Expressions régulières associées aux paramètres génériques
     * @param Verifier[] $accessVerifiers Vérificateurs d'accès à la route, seuls les fonctions failures sont prises en compte et retournent Response
     * @return Route La route créée
     */
    public static function patch(string $link, Closure|array $toExecute, string $routeName, array $genericParamsRegex = [], array $accessVerifiers = []): Route {
        return new Route(
            requestMethod: "patch",
            link: $link,
            toExecute: $toExecute,
            routeName: $routeName,
            genericParamsRegex: $genericParamsRegex,
            accessVerifiers: $accessVerifiers
        );
    }

    /**
     * @brief Crée une route OPTIONS
     * @param string $link Lien de la route
     * @param Closure|array $toExecute Fonction à exécuter pour traiter la route
     * @param string $routeName Nom de la route
     * @param array $genericParamsRegex Expressions régulières associées aux paramètres génériques
     * @param Verifier[] $accessVerifiers Vérificateurs d'accès à la route, seuls les fonctions failures sont prises en compte et retournent Response
     * @return Route La route créée
     */
    public static function options(string $link, Closure|array $toExecute, string $routeName, array $genericParamsRegex = [], array $accessVerifiers = []): Route {
        return new Route(
            requestMethod: "options",
            link: $link,
            toExecute: $toExecute,
            routeName: $routeName,
            genericParamsRegex: $genericParamsRegex,
            accessVerifiers: $accessVerifiers
        );
    }

    /**
     * @brief Crée une route HEAD
     * @param string $link Lien de la route
     * @param Closure|array $toExecute Fonction à exécuter pour traiter la route
     * @param string $routeName Nom de la route
     * @param array $genericParamsRegex Expressions régulières associées aux paramètres génériques
     * @param Verifier[] $accessVerifiers Vérificateurs d'accès à la route, seuls les fonctions failures sont prises en compte et retournent Response
     * @return Route La route créée
     */
    public static function head(string $link, Closure|array $toExecute, string $routeName, array $genericParamsRegex = [], array $accessVerifiers = []): Route {
        return new Route(
            requestMethod: "head",
            link: $link,
            toExecute: $toExecute,
            routeName: $routeName,
            genericParamsRegex: $genericParamsRegex,
            accessVerifiers: $accessVerifiers
        );
    }

    /**
     * @brief Crée une route TRACE
     * @param string $link Lien de la route
     * @param Closure|array $toExecute Fonction à exécuter pour traiter la route
     * @param string $routeName Nom de la route
     * @param array $genericParamsRegex Expressions régulières associées aux paramètres génériques
     * @param Verifier[] $accessVerifiers Vérificateurs d'accès à la route, seuls les fonctions failures sont prises en compte et retournent Response
     * @return Route La route créée
     */
    public static function trace(string $link, Closure|array $toExecute, string $routeName, array $genericParamsRegex = [], array $accessVerifiers = []): Route {
        return new Route(
            requestMethod: "trace",
            link: $link,
            toExecute: $toExecute,
            routeName: $routeName,
            genericParamsRegex: $genericParamsRegex,
            accessVerifiers: $accessVerifiers
        );
    }
}
