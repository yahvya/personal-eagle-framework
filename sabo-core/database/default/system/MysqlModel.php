<?php

namespace SaboCore\Database\Default\System;

use SaboCore\Database\System\DatabaseCondition;
use SaboCore\Database\System\DatabaseCondSeparator;
use SaboCore\Database\System\DatabaseModel;
use SaboCore\Utils\List\SaboList;

class MysqlModel extends DatabaseModel
{

    /**
     * @inheritDoc
     */
    public function create(): bool
    {

    }

    /**
     * @inheritDoc
     */
    public function update(): bool
    {

    }

    /**
     * @inheritDoc
     */
    public function delete(): bool
    {

    }

    /**
     * @inheritDoc
     */
    public static function findOne(DatabaseCondition|DatabaseCondSeparator ...$findBuilders): DatabaseModel|null
    {

    }

    /**
     * @inheritDoc
     */
    public static function findAll(DatabaseCondition|DatabaseCondSeparator ...$findBuilders): SaboList
    {

    }
}