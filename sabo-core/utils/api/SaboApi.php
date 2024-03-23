<?php

namespace SaboCore\Utils\Api;

use Exception;
use ReflectionClass;

/**
 * @brief Utilitaire requête d'api curl
 * @author yahaya bathily https://github.com/yahvya
 */
abstract class SaboApi{ 
    /**
     * @var string préfixe url api
     */
    protected string $apiUrlPrefix;

    /**
     * @var array résultats de requêtes stockés
     */
    protected array $storedRequestResult;

    /**
     * @var string|null valeur de la dernière requête exécutée, null si aucune valeur
     */
    private ?string $lastRequestResult;

    /**
     * @param string $apiUrlPrefix lien préfixant les appels de l'api
     */
    public function __construct(string $apiUrlPrefix){
        $this->apiUrlPrefix = !str_ends_with(haystack: $apiUrlPrefix,needle: "/") && !str_ends_with(haystack: $apiUrlPrefix,needle: "\\") ? $apiUrlPrefix . "/" : $apiUrlPrefix;
        $this->lastRequestResult = null;
        $this->storedRequestResult = [];
    }

    /**
     * @brief Fourni une url basé sur le préfixe de l'api
     * @param string $apiSuffix suffixe à ajouter
     * @return string l'URL composé du préfix de l'api et du suffixe
     */
    protected function apiUrl(string $apiSuffix):string{
        return $this->apiUrlPrefix . $apiSuffix;
    }

    /**
     * @brief Fais une requête curl à partir de la configuration donnée et met en jour en cas de succès lastRequestResult
     * @param string $requestUrl lien de requête (basé sur la fonction apiUrl)
     * @param array $headers en-tête de la requête
     * @param mixed $data données de la requête
     * @param SaboApiRequest $dataConversionType type de conversion de donnée par défault json_encode [JSON_BODY|HTTP_BUILD_QUERY|NO_DATA] NO_DATA si aucune donnée ne doit être affecté
     * @param array $overrideCurlOptions tableau écrasant les options par défaut curl indicé par les constantes d'options curl
     * @param string|null $storeIn si c'est non null sauvegarde le résultat de la requête avec comme indice la clé donné dans l'accessible "storedRequestResult"
     * @return bool si la requête a réussi
     */
    protected function request(string $requestUrl,array $headers,mixed $data,SaboApiRequest $dataConversionType,array $overrideCurlOptions = [],?string $storeIn = null):bool{
        $curl = curl_init();

        if($curl === false) return false;

        // options par défaut
        $options = [
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true
        ];

        // override des options
        foreach($overrideCurlOptions as $curlOption => $value) $options[$curlOption] = $value;

        $options[CURLOPT_HTTPHEADER] = $headers;
        $options[CURLOPT_URL] = $requestUrl;

        if(SaboApiRequest::NO_DATA != $dataConversionType)
            $options[CURLOPT_POSTFIELDS] = $dataConversionType == SaboApiRequest::HTTP_BUILD_QUERY ? http_build_query(data: $data) : @json_encode(value: $data);

        if(!curl_setopt_array(handle: $curl,options: $options) ) return false;

        $result = curl_exec(handle: $curl);

        if($storeIn !== null) $this->storedRequestResult[$storeIn] = $result;

        if($options[CURLOPT_RETURNTRANSFER]){
            if($result === false) return false;

            $this->lastRequestResult = $result;

            return true;
        }
        
        return $result;
    }

    /**
     * @param SaboApiRequest $as défini comment la donnée doit être retournée [RESULT_AS_JSON_ARRAY|RESULT_AS_STRING]
     * @return string|array|null les données de la dernière requête ou null
     */
    protected function getLastRequestResult(SaboApiRequest $as):string|array|null{
        if($this->lastRequestResult == null) return null;

        switch($as){
            case SaboApiRequest::RESULT_AS_JSON_ARRAY : 
                $jsonData = @json_decode(json: $this->lastRequestResult,associative: true);
                
                return gettype(value: $jsonData) != "array" ? null : $jsonData;

            case SaboApiRequest::RESULT_AS_STRING: 
                return $this->lastRequestResult;

            default:
                return null;
        }
    }

    /**
     * @brief Vérifie si le tableau donné contient les clés
     * @param array $toCheck tableau de données
     * @param string ...$keysToCheck clés à vérifier format d'une clé "level1.level2" pour un tableau ["level1" → ["level2" → 2]]
     * @return bool si les clés existent dans le tableau
     */
    protected static function ifArrayContain(array $toCheck,string ...$keysToCheck):bool{
        foreach($keysToCheck as $keyToCheck){
            $arrayCopy = $toCheck;

            $keys = explode(separator: ".",string: $keyToCheck);

            foreach($keys as $key){
                if(gettype(value: $arrayCopy) != "array" || !array_key_exists(key: $key,array: $arrayCopy) ) return false;

                $arrayCopy = $arrayCopy[$key];
            }   
        }

        return true;
    }

    /**
     * @brief Crée un objet à partir de la configuration api
     * @attention à appeler avec la class enfant
     * @param array $config tableaux indicés par SaboApiConfig->value
     * @return mixed l'objet crée ou null
     */
    public static function createFromConfig(array $config):mixed{
        try{
            $reflection = new ReflectionClass(objectOrClass: get_called_class() );

            return $reflection->newInstance(
                $config[SaboApiConfig::URL->value]
            );
        }
        catch(Exception){
            return null;
        }
    } 
}