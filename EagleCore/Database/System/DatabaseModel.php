<?php /** @noinspection ALL */

namespace Yahvya\EagleFramework\Database\System;

use Yahvya\EagleFramework\Config\ConfigException;
use Yahvya\EagleFramework\Utils\List\EagleList;

/**
 * @brief Database model
 */
abstract class DatabaseModel
{
    /**
     * @brief Create the row in the database
     * @return bool Whether the creation succeeded
     * @throws DatabaseActionException On error
     * @throws ConfigException On error
     */
    public abstract function create(): bool;

    /**
     * @brief Actions to perform after model creation
     * @param mixed $datas Data to provide
     * @attention It is recommended to call parent::afterCreate if overridden
     * @return $this
     * @throws DatabaseActionException To stop the action in case of error
     * @throws ConfigException On error
     */
    protected function afterCreate(mixed $datas): DatabaseModel
    {
        return $this;
    }

    /**
     * @brief Actions to perform before model creation
     * @param mixed $datas Data to provide
     * @attention It is recommended to call parent::beforeCreate if overridden
     * @return $this
     * @throws DatabaseActionException To stop the action in case of error
     * @throws ConfigException On error
     */
    protected function beforeCreate(mixed $datas): DatabaseModel
    {
        return $this;
    }

    /**
     * @brief Update the row in the database
     * @return bool Whether the update succeeded
     * @throws DatabaseActionException On error
     * @throws ConfigException On error
     */
    public abstract function update(): bool;

    /**
     * @brief Actions to perform after the model update
     * @param mixed $datas Data to provide
     * @attention It is recommended to call parent::afterUpdate if overridden
     * @return $this
     * @throws DatabaseActionException To stop the action in case of error
     * @throws ConfigException On error
     */
    protected function afterUpdate(mixed $datas): DatabaseModel
    {
        return $this;
    }

    /**
     * @brief Actions to perform before model update
     * @param mixed $datas Data to provide
     * @attention It is recommended to call parent::beforeUpdate if overridden
     * @return $this
     * @throws DatabaseActionException To stop the action in case of error
     * @throws ConfigException On error
     */
    protected function beforeUpdate(mixed $datas): DatabaseModel
    {
        return $this;
    }

    /**
     * @brief Delete the row in the database
     * @return bool Whether the deletion succeeded
     * @throws DatabaseActionException On error
     * @throws ConfigException On error
     */
    public abstract function delete(): bool;

    /**
     * @brief Actions to perform after model deletion
     * @param mixed $datas Data to provide
     * @attention It is recommended to call parent::afterDelete if overridden
     * @return $this
     * @throws DatabaseActionException To stop the action in case of error
     * @throws ConfigException On error
     */
    protected function afterDelete(mixed $datas): DatabaseModel
    {
        return $this;
    }

    /**
     * @brief Actions to perform before model deletion
     * @param mixed $datas Data to provide
     * @attention It is recommended to call parent::beforeDelete if overridden
     * @return $this
     * @throws DatabaseActionException To stop the action in case of error
     * @throws ConfigException On error
     */
    protected function beforeDelete(mixed $datas): DatabaseModel
    {
        return $this;
    }

    /**
     * @brief Actions to perform before generating the model from the find method
     * @param mixed $datas Data to provide
     * @attention It is recommended to call parent::beforeGeneration if overridden
     * @return $this
     * @throws DatabaseActionException To stop the action in case of error
     * @throws ConfigException On error
     */
    protected function beforeGeneration(mixed $datas): DatabaseModel
    {
        return $this;
    }

    /**
     * @brief Actions to perform after generating the model from the find method
     * @param mixed $datas Data to provide
     * @attention It is recommended to call parent::afterGeneration if overridden
     * @return $this
     * @throws DatabaseActionException To stop the action in case of error
     * @throws ConfigException On error
     */
    protected function afterGeneration(mixed $datas): DatabaseModel
    {
        return $this;
    }

    /**
     * @brief Find a single record
     * @param QueryCondition|QueryCondSeparator ...$findBuilders Search configuration
     * @return DatabaseModel|null The found model or null
     */
    public abstract static function findOne(QueryCondition|QueryCondSeparator ...$findBuilders): DatabaseModel|null;

    /**
     * @brief Find all records
     * @param QueryCondition|QueryCondSeparator ...$findBuilders Search configuration
     * @return EagleList<DatabaseModel> List of records
     */
    public abstract static function findAll(QueryCondition|QueryCondSeparator ...$findBuilders): EagleList;
}
