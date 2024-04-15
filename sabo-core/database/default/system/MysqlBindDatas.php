<?php

namespace SaboCore\Database\Default\System;

use SaboCore\Utils\List\SaboList;

/**
 * @brief Données de bind mysql
 * @author yahaya bathily https://github.com/yahvya
 */
class MysqlBindDatas{
    /**
     * @var int Nombre de marqueurs de bind
     */
    protected int $countOfMarkers;

    /**
     * @var SaboList<array> [[tableau contenant dans l'ordre les paramètres de bindValue sans l'index en premier], ...]
     */
    protected SaboList $toBindDatas;

    /**
     * @param int $countOfMarkers Nombre de marqueurs de bind
     * @param array $toBindDatas [[tableau contenant dans l'ordre les paramètres de bindValue sans l'index en premier], ...]
     */
    public function __construct(int $countOfMarkers,array $toBindDatas){
        $this->countOfMarkers = $countOfMarkers;
        $this->toBindDatas = new SaboList(datas: $toBindDatas);
    }

    /**
     * @return string Construis la chaine ?,? de la requête prepare à partir du nombre de marqueurs
     */
    public function getMarkersStr():string{
        return substr(string: str_repeat(string: "?,",times: $this->countOfMarkers),offset: 0,length: -1);
    }

    /**
     * @return int Nombre de marqueurs de bind
     */
    public function getCountOfMarkers(): int{
        return $this->countOfMarkers;
    }

    /**
     * @return SaboList<array> [[tableau contenant dans l'ordre les paramètres de bindValue sans l'index en premier], ...]
     */
    public function getToBindDatas(): SaboList{
        return $this->toBindDatas;
    }
}