<?php

namespace SaboCore\Database\Default\QueryBuilder;

use PDO;
use PDOStatement;
use ReflectionClass;
use SaboCore\Config\ConfigException;
use SaboCore\Database\Default\System\MysqlModel;
use Throwable;

/**
 * @brief Constructeur de requête
 * @author yahaya bathily https://github.com/yahvya
 */
class MysqlQueryBuilder{
    /**
     * @var string Chaine sql de la requête
     */
    protected string $sqlString;

    /**
     * @var array Valeur à bind
     */
    protected array $toBind = [];

    /**
     * @var MysqlModel
     */
    protected MysqlModel $baseModel;

    /**
     * @param string $modelClass class du model
     * @throws ConfigException en cas d'erreur
     */
    public function __construct(string $modelClass){
        try{
            $reflection = new ReflectionClass(objectOrClass: $modelClass);

            $model = $reflection->newInstance();

            if($model instanceof MysqlModel)
                throw new ConfigException(message: "La class fournie doit être une sous class de " . MysqlModel::class);
        }
        catch(Throwable){
            throw new ConfigException(message: "Une erreur s'est produite lors de la construction du builder");
        }
    }

    /**
     * @brief Démarre une requête statique
     * @param string $sqlString requête sql
     * @param array $toBind Valeur à bind
     * @return $this
     */
    public function staticRequest(string $sqlString,array $toBind = []):MysqlQueryBuilder{
        $this->sqlString = $sqlString;
        $this->toBind = $toBind;

        return $this;
    }

    /**
     * @brief Remet à 0 le contenu du QueryBuilder
     * @return $this
     */
    public function reset():MysqlQueryBuilder{
        $this->sqlString = "";
        $this->toBind = [];

        return $this;
    }

    /**
     * @brief Prépare la requête
     * @param PDO $pdo instance pdo
     * @return PDOStatement|null Résultat de la préparation
     */
    public function prepareRequest(PDO $pdo):?PDOStatement{
        try{
            return $pdo->prepare("");
        }
        catch(Throwable){
            return null;
        }
    }
}