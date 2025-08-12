<?php

namespace Yahvya\EagleFramework\Database\Default\System;

use Yahvya\EagleFramework\Utils\List\EagleList;

/**
 * @brief Mysql bind data
 */
class MysqlBindDatas
{
    /**
     * @var EagleList<array> Eagle list of [[array containing in order, the bindValue method parameters values without the index at first], ...]
     */
    protected(set) EagleList $dataToBind;

    /**
     * @param int $countOfMarkers Count of markers to bind
     * @param array $toBindDatas [[array containing in order, the bindValue method parameters values without the index at first], ...]
     */
    public function __construct(protected(set) int $countOfMarkers, array $toBindDatas)
    {
        $this->dataToBind = new EagleList(datas: $toBindDatas);
    }

    /**
     * @return string Build the string: "?,?" of the "prepare" request from the count of markers
     */
    public function getMarkersStr(): string
    {
        return substr(string: str_repeat(string: "?,", times: $this->countOfMarkers), offset: 0, length: -1);
    }
}