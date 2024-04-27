<?php

namespace SaboCore\Database\Default\CustomDatatypes;

use SaboCore\Database\Default\Attributes\JoinedColumn;
use SaboCore\Database\Default\System\MysqlException;
use SaboCore\Database\Default\System\MysqlModel;
use SaboCore\Utils\List\SaboList;

/**
 * @brief Lignes jointes
 * @author yahaya bathily https://github.com/yahvya
 * @template ContainedType type d'éléments contenu
 */
class JoinedList extends SaboList {
    /**
     * @var JoinedColumn Descripteur de la jointure
     */
    protected JoinedColumn $descriptor;

    /**
     * @var MysqlModel Model lié
     */
    protected MysqlModel $linkedModel;

    /**
     * @param JoinedColumn $descriptor Descripteur de la jointure
     * @param MysqlModel $linkedModel model lié
     */
    public function __construct(JoinedColumn $descriptor,MysqlModel $linkedModel){
        parent::__construct(datas: []);

        $this->descriptor = $descriptor;
        $this->linkedModel = $linkedModel;
    }

    /**
     * @brief Charge les données de la jointure
     * @return $this
     * @throws MysqlException en cas d'erreur lors du chargement
     */
    public function loadContent():JoinedList{
        $this->datas = MysqlModel::loadJoinedColumns(model: $this->linkedModel,joinedColumn: $this->descriptor)->toArray();
        $this->currentPos = 0;

        return $this;
    }
}