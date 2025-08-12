<?php

namespace Yahvya\EagleFramework\Database\Default\CustomDatatypes;

use Yahvya\EagleFramework\Database\Default\Attributes\JoinedColumn;
use Yahvya\EagleFramework\Database\Default\System\MysqlException;
use Yahvya\EagleFramework\Database\Default\System\MysqlModel;
use Yahvya\EagleFramework\Utils\List\EagleList;

/**
 * @brief Joined lines custom type
 * @template ContainedType Contained element types
 */
class JoinedList extends EagleList
{
    /**
     * @param JoinedColumn $descriptor Join column descriptor
     * @param MysqlModel $linkedModel Linked model
     */
    public function __construct(
        protected(set) JoinedColumn $descriptor,
        protected(set) MysqlModel $linkedModel
    )
    {
        parent::__construct(datas: []);
    }

    /**
     * @brief Load the joined data
     * @return $this
     * @throws MysqlException On error
     */
    public function loadContent(): JoinedList
    {
        $this->datas = MysqlModel::loadJoinedColumns(model: $this->linkedModel, joinedColumn: $this->descriptor)->toArray();
        $this->currentPos = 0;

        return $this;
    }
}