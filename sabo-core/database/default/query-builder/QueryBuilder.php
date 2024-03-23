<?php

namespace SaboCore\Database\Default\QueryBuilder;

use Exception;
use SaboCore\Config\EnvConfig;
use SaboCore\Database\Default\Model\SaboModel;
use SaboCore\Routing\Application\Application;

/**
 * @brief Constructeur de requêtes
 * @author yahaya bathily https://github.com/yahvya
 */
class QueryBuilder{
    use Select;
    use Where;
    use Option;
    use Delete;
    use Update;
    use Insert;
    use Join;

    /**
     * @brief Valeurs à bind pour la requête
     */
    private array $toBind;

    /**
     * @brief La chaine représentant la requête
     */
    private string $sqlString;

    /**
     * @brief Représente l'alias de la table
     */
    private string $as;

    /**
     * @brief Le model lié au builder
     */
    private SaboModel $linkedModel;

    /**
     * @param SaboModel $linkedModel le model à lier à la requête
     */
    public function __construct(SaboModel $linkedModel){
        $this->linkedModel = $linkedModel;
        $this->as = $this->linkedModel->getTableName();
        $this->reset();
    }

    /**
     * @brief Modifie l'alias de la table
     * @param string $as l'alias
     * @return $this
     */
    public function as(string $as):QueryBuilder{
        $this->sqlString = str_replace(search: "$this->as.",replace: "$as.",subject: $this->sqlString);
        $this->sqlString = str_replace(search: "AS $this->as",replace: "AS $as",subject: $this->sqlString);
        
        $this->as = $as;

        return $this;
    }

    /**
     * @brief Défini une requête personnalisée
     * @param string $sqlString la chaine sql
     * @param array $toBind valeurs à bind à l'exécution
     * @return $this
     */
    public function customQuery(string $sqlString,array $toBind):QueryBuilder{
        $this->sqlString = $sqlString;
        $this->toBind = $toBind;
        
        return $this;
    }

    /**
     * @brief Remet à 0 le contenu du builder
     * @return $this
     */
    public function reset():QueryBuilder{
        $this->toBind = [];
        $this->sqlString = "";

        return $this;
    }

    /**
     * @return string la chaine sql
     */
    public function getSqlString():string{
        return $this->sqlString;
    }   

    /**
     * @return array les valeurs à bind
     */
    public function getToBind():array{
        return $this->toBind;
    }

    /**
     * @brief Récupère le nom de la colonne lié à un attribut du model
     * @param string $attributeName nom de l'attribut
     * @param bool $includeAs définis si l'alias doit être inclus
     * @return string|null le nom de la colonne lié ou null en cas d'échec
     * @throws Exception (en mode debug)
     */
    public function getAttributeLinkedColName(string $attributeName,bool $includeAs = false):?string{
        $columnsConfiguration = $this->linkedModel->getColumnsConfiguration();

        if(empty($columnsConfiguration[$attributeName]) || empty($columnsConfiguration[$attributeName]["configClass"]) ){
            if(Application::getEnvConfig()->getConfig(name: EnvConfig::DEV_MODE_CONFIG->value) )
                throw new Exception(message: "Attribut inconnu ou non lié à une colonne sur select $attributeName");
            else    
                return null;
        }

        return $includeAs ?
            "$this->as.{$columnsConfiguration[$attributeName]["configClass"]->getLinkedColName()}" :
            $columnsConfiguration[$attributeName]["configClass"]->getLinkedColName();
    }

    /**
     * @brief Ajoute les clés primaires comme condition where
     * @return $this
     * @throws Exception en mode debug si aucune clé trouvée
     */
    public function addPrimaryKeysWhereCond():QueryBuilder{
        $whereCondArray = [];

        // récupération des clés primaires
        foreach($this->linkedModel->getColumnsConfiguration() as $attributeName => $columnConfiguration){
            if(!empty($columnConfiguration["configClass"]) && $columnConfiguration["configClass"]->getIsPrimaryKey() ){
                $whereCondArray[] = [$attributeName, $this->linkedModel->getAttribute(attributeName: $attributeName), SqlComparator::EQUAL, SqlSeparator::AND];
            }
        }

        $size = count(value: $whereCondArray);

        if($size == 0){
            if(Application::getEnvConfig()->getConfig(name: EnvConfig::DEV_MODE_CONFIG->value))
                throw new Exception(message: "Il n'y a pas de clé primaire");
            else 
                return $this;
        }

        unset($whereCondArray[$size - 1][3]);

        $this
            ->where()
            ->whereGroup(...$whereCondArray);

        return $this;
    } 

    /**
     * @return SaboModel le model lié
     */
    public function getLinkedModel():SaboModel{
        return $this->linkedModel;
    }

    /**
     * @brief Crée un QueryBuilder à partir de la classe donnée
     * @param string $modelClass la class du model à lier
     * @return QueryBuilder|null le QueryBuilder ou null en cas d'échec
     * @throws Exception (en mode debug)
     */
    public static function createFrom(string $modelClass):?QueryBuilder{
        if(!class_exists(class: $modelClass) || !is_subclass_of(object_or_class: $modelClass,class: SaboModel::class) ){
            if(Application::getEnvConfig()->getConfig(name: EnvConfig::DEV_MODE_CONFIG->value) )
                throw new Exception(message: "La class $modelClass passé à QueryBuilder doit être une sous classe de SaboModel");
            else
                return null;
        }

        return new QueryBuilder(linkedModel: new $modelClass() );
    } 
}