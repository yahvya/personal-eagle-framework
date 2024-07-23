<?php

namespace SaboCore\Routing\Request;

use SaboCore\Treatment\TreatmentException;
use SaboCore\Utils\FileManager\FormFileManager;
use SaboCore\Utils\Session\SessionStorage;

/**
 * @brief Gestionnaire des données de la requête
 * @author yahaya bathily https://github.com/yahvya
 */
class Request{
    /**
     * @var SessionStorage gestionnaire de stockage de la session
     */
    protected SessionStorage $sessionStorage;

        /**
         * @var array en-têtes de la requête
         */
        protected array $headers;

        public function __construct(){
            $this->sessionStorage = SessionStorage::create();
            $headers = apache_request_headers();
            $this->headers = $headers !== false ? $headers : [];
        }

        /**
         * @return SessionStorage le gestionnaire de stockage de la session
         */
        public function getSessionStorage():SessionStorage{
            return $this->sessionStorage;
        }

        /**
         * @brief Recherche des données post
         * @param string|null $errorMessage Si valeur non nulle une exception TreatmentException est levé en cas de clé non trouvée avec ce message displayable
         * @param string ...$toGet Les clés à chercher
         * @return array|null Les valeurs trouvées indicées par leurs clés ou null si une clé non trouvée et message d'erreur null
         * @throws TreatmentException En cas de clé non trouvée et message d'erreur présent
         */
        public function getPostValues(?string $errorMessage = null,string ...$toGet):?array{
            $values = self::getValuesFrom($_POST,...$toGet);

            if($values === null){
                if($errorMessage !== null)
                    throw new TreatmentException(message: $errorMessage,isDisplayable: true);

                return null;
            }

            return $values;
        }

        /**
         * @brief Recherche des données get
         * @param string|null $errorMessage Si valeur non nulle une exception TreatmentException est levé en cas de clé non trouvée avec ce message displayable
         * @param string ...$toGet Les clés à chercher
         * @return array|null Les valeurs trouvées indicées par leurs clés ou null si une clé non trouvée et message d'erreur null
         * @throws TreatmentException En cas de clé non trouvée et message d'erreur présent
         */
        public function getGetValues(?string $errorMessage = null,string ...$toGet):?array{
            $values = self::getValuesFrom($_GET,...$toGet);

            if($values === null){
                if($errorMessage !== null)
                    throw new TreatmentException(message: $errorMessage,isDisplayable: true);

                return null;
            }

            return $values;
        }

        /**
         * @brief Recherche des données cookies
         * @param string|null $errorMessage Si valeur non nulle une exception TreatmentException est levé en cas de clé non trouvée avec ce message displayable
         * @param string ...$toGet Les clés à chercher
         * @return array|null Les valeurs trouvées indicées par leurs clés ou null si une clé non trouvée et message d'erreur null
         * @throws TreatmentException En cas de clé non trouvée et message d'erreur présent
         */
        public function getCookieValues(?string $errorMessage = null,string ...$toGet):?array{
            $values = self::getValuesFrom($_COOKIE,...$toGet);

            if($values === null){
                if($errorMessage !== null)
                    throw new TreatmentException(message: $errorMessage,isDisplayable: true);

                return null;
            }

            return $values;
        }

        /**
         * @brief Recherche des données files
         * @param string|null $errorMessage Si valeur non nulle une exception TreatmentException est levé en cas de clé non trouvée avec ce message displayable
         * @param string ...$toGet Les clés à chercher
         * @return array<string,FormFileManager>|null Les valeurs trouvées (sous forme de l'utilitaire FormFileManager) indicées par leurs clés ou null si une clé non trouvée et message d'erreur null
         * @throws TreatmentException En cas de clé non trouvée et message d'erreur présent
         */
        public function getFilesValues(?string $errorMessage = null,string ...$toGet):?array{
            $values = self::getValuesFrom($_FILES,...$toGet);

            if($values === null){
                if($errorMessage !== null)
                    throw new TreatmentException(message: $errorMessage,isDisplayable: true);

                return null;
            }

            // création des FormFileManager
            foreach($values as $key => $file)
                $values[$key] = new FormFileManager(fileDatas: $file);

            return $values;
        }

        /**
         * @brief Fourni un en tête requête
         * @param string $header nom de l'en tête
         * @return string|null l'en-tête ou null si non trouvé
         */
        public function getHeader(string $header):string|null{
            return $this->headers[$header] ?? null;
        }

        /**
         * @return string la méthode de requêtr (post,get ...) en lowercase
         */
        public function getMethod():string{
            return strtolower(string: $_SERVER["REQUEST_METHOD"]);
        }

        /**
         * @brief Récupère les valeurs présentes dans un conteneur
         * @param array $container le conteneur
         * @param string ...$toGet clé à rechercher
         * @return array|null le tableau des valeurs trouvées ou null si une des clés n'a pas été trouvée
         */
        protected static function getValuesFrom(array $container,string ...$toGet):?array{
            $result = [];

            foreach($toGet as $key){
                if(!array_key_exists(key: $key,array: $container) )
                    return null;

                $result[$key] = $container[$key];
            }

            return $result;
        }
    }