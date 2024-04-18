<?php

namespace SaboCore\Database\Default\CustomDatatypes;

use DateTime;

/**
 * @brief Type custom timestamp
 * @author yahaya bathily https://github.com/yahvya
 */
class Timestamp{
    /**
     * @var DateTime Gestionnaire interne de la date
     */
    protected DateTime $dateManager;

    /**
     * @param int|null $timestamp Timestamp à gérer. Si null timestamp actuel utilisé
     */
    public function __construct(?int $timestamp = null){
        if($timestamp === null)
            $timestamp = time();

        $this->dateManager = new DateTime();
        $this->dateManager->setTimestamp($timestamp);
    }

    /**
     * @return int le timestamp converti en entrée pour la base de donnée
     */
    public function convertForDatabase():int{
        return $this->dateManager->getTimestamp();
    }

    /**
     * @return int Fourni le timestamp
     */
    public function getTimestamp():int{
        return $this->dateManager->getTimestamp();
    }

    /**
     * @return DateTime fourni un datetime à partir du timestamp interne
     */
    public function toDateTime():DateTime{
        return clone $this->dateManager;
    }

    /**
     * @brief Formate le timestamp
     * @param string $format format
     * @return string le retour
     */
    public function format(string $format = "Y-m-d H:i:s"):string{
        return $this->dateManager->format(format: $format);
    }

    /**
     * @brief Converti la donnée fournie en instance de timestamp
     * @param string $data timestamp stocké
     * @return Timestamp Le timestamp généré
     */
    public static function fromDatabase(mixed $data):Timestamp{
        return new Timestamp(timestamp: intval(value: $data));
    }
}