<?php

namespace SaboCore\Database\Default\Model;

use PDO;
use Exception;
use PDOException;
use PDOStatement;
use SaboCore\Config\DatabaseConfig;
use SaboCore\Config\EnvConfig;
use SaboCore\Database\Default\QueryBuilder\QueryBuilder;
use SaboCore\Database\Default\QueryBuilder\SqlComparator;
use SaboCore\Database\Default\QueryBuilder\SqlSeparator;
use SaboCore\Routing\Application\Application;
use Throwable;

/**
 * @brief représente une base de donnée MYSQL
 * @author yahaya bathily https://github.com/yahvya
 */
abstract class SaboMysql implements System{
    /**
     * @brief connexion partagée entre les modèles
     */
    protected static ?PDO $sharedCon = null;

    /**
     * @brief Connexion du modèle
     */
    protected ?PDO $myCon = null;

    /**
     * @brief Constructeur de requête lié au modèle
     */
    protected ?QueryBuilder $queryBuilder = null;

    /**
     * @brief Remplace le constructeur de la class
     * @throws Throwable en cas d'erreur
     */
    abstract protected function pseudoConstruct():void;

    /**
     * @throws Throwable en cas d'erreur
     */
    public function __construct(){
        $this->pseudoConstruct();
        
        $this->myCon = self::$sharedCon;
    }

    /**
     * @brief insère le model dans la base de données
     * @return bool si la requête a réussi
     */
    public function insert():bool{
        try{
            $this->initQueryBuilder();

            $linkedModel = $this->queryBuilder->getLinkedModel();

            $toInsert = [];

            // création du tableau de valeur à insérer
            foreach($linkedModel->getColumnsConfiguration() as $attributeName => $configuration){
                if(!empty($configuration["configClass"]) && !$configuration["configClass"]->getIsAutoIncremented() )
                    $toInsert[$attributeName] = $linkedModel->getAttribute(attributeName: $attributeName);
            }

            $this->queryBuilder->insert(values: $toInsert);

            return self::execQuery(queryBuilder: $this->queryBuilder);
        }
        catch(Throwable){
            return false;
        }
    }

    /**
     * @brief Supprime le model de la base de donnée
     * @return bool si la requête a réussi 
     */
    public function delete():bool{
        try{
            $this->initQueryBuilder();

            $this->queryBuilder
                ->delete()
                ->addPrimaryKeysWhereCond();

            return self::execQuery(queryBuilder: $this->queryBuilder);
        }
        catch(Throwable){
            return false;
        }
    }

    /**
     * @brief Supprime le model de la base de donnée
     * @return bool si la requête a réussi 
     */
    public function update():bool{
        try{
            $this->initQueryBuilder();

            $linkedModel = $this->queryBuilder->getLinkedModel();
            $columnsConfiguration = $linkedModel->getColumnsConfiguration();

            $toUpdate = [];

            // création du tableau de valeur à update
            foreach($columnsConfiguration as $attributeName => $configuration){
                if(!empty($configuration["configClass"]) )
                    $toUpdate[$attributeName] = $linkedModel->getAttribute(attributeName: $attributeName);
            }

            $this->queryBuilder
                ->update(toUpdate: $toUpdate)
                ->addPrimaryKeysWhereCond();

            return self::execQuery(queryBuilder: $this->queryBuilder);
        }
        catch(Throwable){
            return false;
        }
    }

    /**
     * @brief démarre une transaction sur la connexion du model
     * @throws Exception (en mode debug) si la connexion est null
     */
    public function beginTransaction():bool{
        return self::beginTransactionOn(con: $this->myCon);
    }

    /**
     * @brief commit une transaction sur la connexion du model
     * @throws Exception (en mode debug) si la connexion est null
     */
    public function commitTransaction():bool{
        return self::commitTransactionOn(con: $this->myCon);
    }

    /**
     * @brief rollback une transaction sur la connexion du model
     * @throws Exception (en mode debug) si la connexion est null
     */
    public function rollbackTransaction():bool{
        return self::rollbackTransactionOn(con: $this->myCon);
    }

    /**
     * @return PDO|null la connexion
     */
    public function getMyCon():?PDO{
        return $this->myCon;
    }

    /**
     * @param bool $resetBefore défini si le QueryBuilder doit être reset avant d'être renvoyé
     * @return QueryBuilder le query builder interne au model (non reset)
     */
    public function getQueryBuilder(bool $resetBefore = true):QueryBuilder{
        if($this->queryBuilder == null) 
            $this->initQueryBuilder();
        else if($resetBefore)
            $this->queryBuilder->reset();

        return $this->queryBuilder;
    }

    /**
     * @brief initialise le créateur de requête interne si non défini
     */
    protected function initQueryBuilder():SaboMysql{
        if($this->queryBuilder == null) $this->queryBuilder = new QueryBuilder(linkedModel: $this);

        $this->queryBuilder->reset();

        return $this;
    }

    /**
     * @brief Cherche des résultats en base de données à partir de conditions
     * @param array $conditions conditions à vérifier, format [attribute_name => value] ou [attribute_name => [value,SqlComparator,(non obligatoire and par défaut)] SqlSeparator and ou or]
     * @param array $toSelect le nom des attributs liés aux colonnes à récupérer
     * @param bool $getBaseResult défini si les résultats doivent être retournés telles qu'elles (PDOStatement) ou sous forme d'objets
     * @return PDOStatement|SaboModel[]|bool|null un tableau contenant les objets si résultats multiples ou PDOStatement de la requête si getBaseResult à true ou null si aucun résultat
     * @throws Throwable (en mode debug) si données mal formulées
     */
    public static function find(array $conditions = [], array $toSelect = [], bool $getBaseResult = false): PDOStatement|array|bool|null
    {
        $queryBuilder = QueryBuilder::createFrom(modelClass: get_called_class() );

        $queryBuilder->select(...$toSelect);

        // vérification des conditions
        if(!empty($conditions) ){
            $whereConditions = [];

            // création des conditions where
            foreach($conditions as $attributeName => $condData){
                if(gettype(value: $condData) == "array"){
                    $data = [
                        $attributeName,
                        ...$condData
                    ];

                    if(empty($condData[2]) ) $data[] = SqlSeparator::AND;

                    $whereConditions[] = $data;
                }
                else $whereConditions[] = [$attributeName, $condData, SqlComparator::EQUAL, SqlSeparator::AND];
            }

            // suppression du dernier séparateur and ou or
            unset($whereConditions[count($whereConditions) - 1][3]);

            // ajout de la clause where
            $queryBuilder   
                ->where()
                ->whereGroup(...$whereConditions);
        }

        return self::execQuery(queryBuilder: $queryBuilder,toReturn: $getBaseResult ? MysqlReturn::DEFAULT : MysqlReturn::OBJECTS);
    }

    /**
     * @brief Initialise les modèles
     * @return bool état de succès
     */
    public static function initModel():bool{
        try{
            self::$sharedCon = Application::getEnvConfig()
                ->getConfig(name: EnvConfig::DATABASE_CONFIG->value)
                ->getConfig(name: DatabaseConfig::PROVIDER->value)
                ->getCon();

            return self::$sharedCon != null;
        }
        catch(Throwable){
            return false;
        }
    }

    /**
     * @brief Démarre une transaction sur la connexion partagée
     * @throws Exception (en mode debug) si la connexion est null
     */
    public static function beginTransactionOnShared():bool{
        return self::beginTransactionOn(con: self::$sharedCon);
    }

    /**
     * @brief Commit une transaction sur la connexion partagée
     * @throws Exception (en mode debug) si la connexion est null
     */
    public static function commitTransactionOnShared():bool{
        return self::commitTransactionOn(con: self::$sharedCon);
    }

    /**
     * @brief Rollback une transaction sur la connexion partagée
     * @throws Exception (en mode debug) si la connexion est null
     */
    public static function rollbackTransactionOnShared():bool{
        return self::rollbackTransactionOn(con: self::$sharedCon);
    }

    /**
     * @brief Execute la requête contenue dans un le queryBuilder
     * @param QueryBuilder $queryBuilder le queryBuilder à exécuter
     * @param MysqlReturn $toReturn le type de donnée à retourner, par défaut success_state
     * @return PDOStatement|array|bool|null
     * @throws Throwable en cas d'erreur
     */
    public static function execQuery(QueryBuilder $queryBuilder,MysqlReturn $toReturn = MysqlReturn::SUCCESS_STATE): PDOStatement|array|bool|null{
        $linkedModel = $queryBuilder->getLinkedModel();
        
        $pdo = $linkedModel->getMyCon();

        $query = $pdo->prepare(query: $queryBuilder->getSqlString() );

        if($query !== false){
            // ajout des valeurs à bind
            $toBind = $queryBuilder->getToBind();

			foreach($toBind as $key => $bindData)
			{
				if(gettype($bindData) == "array")
					$query->bindValue($key + 1,...$bindData);
				else
					$query->bindValue($key + 1,gettype(value: $bindData) == "boolean" ? ($bindData === false ? 0 : 1) : $bindData);
			}

			if($query->execute() ){
                switch($toReturn){
                    case MysqlReturn::DEFAULT: return $query;
                    case MysqlReturn::SUCCESS_STATE : return true;
                    case MysqlReturn::OBJECTS:
                        // création des objets model à retourner
                        $objects = [];   

                        foreach($query->fetchAll() as $rowData)
                            $objects[] = static::createObjectFrom(linkedModel: $linkedModel,rowData:  $rowData);

                        return $objects;
                }
            }
		}

        return match ($toReturn) {
            MysqlReturn::DEFAULT => null,
            MysqlReturn::SUCCESS_STATE => false,
            MysqlReturn::OBJECTS => []
        };
    }

    /**
     * @return PDO|null la connexion partagée entre les modèles
     */
    public static function getSharedCon():?PDO{
        return self::$sharedCon;
    }

    /**
     * @brief Lance une transaction avec la connexion
     * @param PDO|null $con la connexion sur laquelle démarrer la connexion
     * @return bool si réussi
     * @throws Exception (en mode debug) si la connexion est null
     */
    protected static function beginTransactionOn(?PDO $con):bool{
        if($con == null){
            if(Application::getEnvConfig()->getConfig(name: EnvConfig::DEV_MODE_CONFIG->value) )
                throw new Exception(message: "La connexion est null, démarrage de transaction échoué");
            else
                return false;
        }   

        try{
            return $con->beginTransaction();
        }
        catch(PDOException){
            return false;
        }
    }

    /**
     * @brief Commit une transaction avec la connexion
     * @param PDO|null $con la connexion sur laquelle commit la connexion
     * @return bool si réussi
     * @throws Exception (en mode debug) si la connexion est null
     */
    protected static function commitTransactionOn(?PDO $con):bool{
        if($con == null){
            if(Application::getEnvConfig()->getConfig(name: EnvConfig::DEV_MODE_CONFIG->value) )
                throw new Exception(message: "La connexion est null, commit de transaction échoué");
            else
                return false;
        }   

        try{
            return $con->commit();
        }
        catch(PDOException){
            return false;
        }
    }

    /**
     * @brief Rollback une transaction avec la connexion
     * @param PDO|null $con la connexion sur laquelle rollback la connexion
     * @return bool si réussi
     * @throws Exception (en mode debug) si la connexion est null
     */
    protected static function rollbackTransactionOn(?PDO $con):bool{
        if($con == null){
            if(Application::getEnvConfig()->getConfig(name: EnvConfig::DEV_MODE_CONFIG->value) )
                throw new Exception(message: "La connexion est null, commit de transaction échoué");
            else
                return false;
        }   

        try{
            return $con->rollBack();
        }
        catch(PDOException){
            return false;
        }
    }

    /**
     * @brief Crée un objet SaboModel à partir de la ligne passé en base de donnée
     * @param SaboModel $linkedModel une instance du model à créer
     * @param array $rowData la ligne de la base de données [format fetchAssoc]
     * @return SaboModel|null le model ou null
     * @throws Exception (en mode debug) si rowData est mal formé
     */
    protected static function createObjectFrom(SaboModel $linkedModel,array $rowData):?SaboModel{
        if(!empty($rowData) ){
            $columnsConfiguration = $linkedModel->getColumnsConfiguration();

            // création d'une nouvelle instance du model
            $model = $linkedModel->getReflection()->newInstance();

            foreach($rowData as $attributeCol => $value){
                $foundName = false;

                // recherche du nom de l'attribut lié à cette colonne
                foreach($columnsConfiguration as $attributeName => $configuration){
                    if(empty($configuration["configClass"]) ) continue;

                    if($configuration["configClass"]->getLinkedColName() == $attributeCol){
                        $foundName = true;

                        $model->{$attributeName} = $value;

                        break;
                    }
                }

                if(!$foundName){
                    if(Application::getEnvConfig()->getConfig(name: EnvConfig::DEV_MODE_CONFIG->value) )
                        throw new Exception(message: "Aucun attribut trouvé pour la colonne $attributeCol");
                    else
                        return null;
                }
            }

            // création des objets liés
            $joinedLinks = $model->getJoinedLinks();

            foreach($joinedLinks as $propertyName => $joinColumnAttribute){
                $linkedSelectors = $joinColumnAttribute->getLinkedSelectors();
                $whereConditions = [];

                // création des conditions de sélection 
                foreach($linkedSelectors as $toCheckCond => $attributeValueName) $whereConditions[$toCheckCond] = $model->$attributeValueName;

                $model->$propertyName = call_user_func_array(callback: [$joinColumnAttribute->getLinkedModelClass(),"find"],args: [$whereConditions]);
            }

            return $model;
        }
        else if(Application::getEnvConfig()->getConfig(name: EnvConfig::DEV_MODE_CONFIG->value) )
            throw new Exception(message: "Le row data est mal formé");

        return null;
    }
}