<?php

namespace Sabo\Model\System\QueryBuilder;

use Exception;
use Sabo\Config\SaboConfig;
use Sabo\Config\SaboConfigAttributes;
use Sabo\Model\Model\SaboModel;

/**
 * constructeur de requêtre
 */
class QueryBuilder{
    use Select;
    use Where;
    use Option;
    use Delete;
    use Update;
    use Insert;

    /**
     * valeur à bind pour la requête
     */
    private array $toBind;

    /**
     * la chaine représentant la requête
     */
    private string $sqlString;

    /**
     * représente l'alias de la table
     */
    private string $as;

    /**
     * le model lié au builder
     */
    private SaboModel $linkedModel;

    /**
     * @param linkedModel le modèle à lié à la requête
     */
    public function __construct(SaboModel $linkedModel){
        $this->linkedModel = $linkedModel;
        $this->as = $this->linkedModel->getTableName();
        $this->reset();
    }

    /**
     * modifie l'alias de la table
     */
    public function as(string $as):QueryBuilder{
        $this->sqlString = str_replace("{$this->as}.","{$as}.",$this->sqlString);
        $this->sqlString = str_replace("as {$this->as}","as {$as}",$this->sqlString);
        
        $this->as = $as;

        return $this;
    }

    /**
     * défini une requête personnalisé
     * @param sqlString la chaine sql
     * @param toBind valeurs à bind à l'exécution
     * @return this
     */
    public function customQuery(string $sqlString,array $toBind):QueryBuilder{
        $this->sqlString = $sqlString;
        $this->toBind = $toBind;
        
        return $this;
    }

    /**
     * remet à 0 le contenu du builder
     * @return this
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
     * récupére le nom de la colonne lié à un attribut du model
     * @param attributeName nom de l'attribut
     * @return string|null le nom de la colonne lié ou null en cas d'échec
     * @throws Exception (en mode debug)
     */
    public function getAttributeLinkedColName(string $attributeName):?string{
        $columnsConfiguration = $this->linkedModel->getColumnsConfiguration();

        if(empty($columnsConfiguration[$attributeName]) || empty($columnsConfiguration[$attributeName]["configClass"]) ){
            if(SaboConfig::getBoolConfig(SaboConfigAttributes::DEBUG_MODE) )
                throw new Exception("Attribut inconnnu ou non lié à une colonne sur select {$attributeName}");
            else    
                return null;
        }

        return $columnsConfiguration[$attributeName]["configClass"]->getLinkedColName();
    }

    /**
     * ajoute les clés primaires comme condition where
     * @return this
     * @throws Exception en mode debug si aucune clé trouvé
     */
    public function addPrimaryKeysWhereCond():QueryBuilder{
        $whereCondArray = [];

        // récupération des clé primaires
        foreach($this->linkedModel->getColumnsConfiguration() as $attributeName => $columnConfiguration){
            if(!empty($columnConfiguration["configClass"]) && $columnConfiguration["configClass"]->getIsPrimaryKey() ){
                array_push($whereCondArray,[$attributeName,$this->linkedModel->getAttribute($attributeName),SqlComparator::EQUAL,SqlSeparator::AND]);
            }
        }

        $size = count($whereCondArray);

        if($size == 0){
            if(SaboConfig::getBoolConfig(SaboConfigAttributes::DEBUG_MODE))
                throw new Exception("Il n'y a pas de clé primaire");
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
     * crée un querybuilder à partir de la classe donnée
     * @param modelClass la class du model à lié
     * @return QueryBuilder|null le querybuilder ou null en cas d'échec
     * @throws Exception (en mode debug)
     */
    public static function createFrom(string $modelClass):?QueryBuilder{
        if(!class_exists($modelClass) || !is_subclass_of($modelClass,SaboModel::class) ){
            if(SaboConfig::getBoolConfig(SaboConfigAttributes::DEBUG_MODE) )
                throw new Exception("La class {$modelClass} passé à querybuilder doit être une sous classe de SaboModel");
            else
                return null;
        }

        return new QueryBuilder(new $modelClass() );
    } 
}