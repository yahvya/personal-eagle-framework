<?php

namespace SaboCore\Treatment;

use SaboCore\Database\Default\Exception\ModelCondException;

/**
 * @brief Gestionnaire de traitement
 * @author yahaya bathily https://github.com/yahvya
 */
abstract class Treatment{
    /**
     * @brief Lèves une exception de traitement
     * @param string $errorMessage message d'erreur
     * @param bool $isDisplayable si le message peut être affiché
     * @return void
     * @throws TreatmentException l'exception
     */
    protected static function throwException(string $errorMessage,bool $isDisplayable = true):void{
        throw new TreatmentException($errorMessage,$isDisplayable);
    }

    /**
     * @brief Lèves une exception de traitement
     * @param ModelCondException $exception l'exception de condition
     * @return void
     * @throws TreatmentException l'exception
     */
    protected static function throwModelCondException(ModelCondException $exception):void{
        throw new TreatmentException($exception->getMessage(),$exception->getIsDisplayable() );
    }
}