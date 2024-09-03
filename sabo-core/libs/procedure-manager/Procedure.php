<?php

namespace PhpAddons\ProcedureManager;

use ReflectionClass;
use Throwable;

/**
 * @brief Gestionnaire de procédure
 * @author yahaya https//github.com/yahvya
 */
abstract class Procedure{
    /**
     * @return ProcedureStep[] étapes de la procédure
     */
    abstract public function getSteps():array;

    /**
     * @param int $currentStep étape actuelle de la procédure (1 à x)
     */
    public function __construct(protected int $currentStep = 1){
    }

    /**
     * @return int numéro de l'étape actuelle
     */
    public function getCurrentStepNumber():int{
        return $this->currentStep;
    }

    /**
     * @return ProcedureStep|null l'étape actuelle ou null
     */
    public function getCurrentStep():?ProcedureStep{
        return $this->getSteps()[$this->currentStep - 1] ?? null;
    }

    /**
     * @param int $stepNumber étape 1 a x
     * @return ProcedureStep|null l'étape ou null
     */
    public function getStep(int $stepNumber):?ProcedureStep{
        return $this->getSteps()[$stepNumber - 1] ?? null;
    }

    /**
     * @return ProcedureStep|null l'étape précédente ou null
     */
    public function getPreviousStep():?ProcedureStep{
        return $this->getSteps()[$this->currentStep - 2] ?? null;
    }

    /**
     * @return ProcedureStep|null l'étape suivante ou null
     */
    public function getNextStep():?ProcedureStep{
        return $this->getSteps()[$this->currentStep] ?? null;
    }

    /**
     * @brief Recule d'une étape
     * @return ProcedureStep|null l'étape ou null
     */
    public function previous():?ProcedureStep{
        $steps = $this->getSteps();

        if(array_key_exists(key: $this->currentStep - 2,array: $steps)){
            $this->currentStep--;
            return $this->getCurrentStep();
        }

        return null;
    }

    /**
     * @brief Avance d'une étape
     * @param bool $checkAccess si true vérifie la validation de l'étape actuelle avant d'avancer
     * @attention si des arguments sont attendues par la fonction de vérification mieux vaux faire l'avancée en 2 étapes
     * @return ProcedureStep|null l'étape ou null
     */
    public function next(bool $checkAccess = true):?ProcedureStep{
        if($checkAccess && !$this->canAccessNext())
            return null;

        $steps = $this->getSteps();

        if(array_key_exists(key: $this->currentStep,array: $steps)){
            $this->currentStep++;
            return $this->getCurrentStep();
        }

        return null;
    }

    /**
     * @return string version serializé de la class pouvant être sauvegardé
     * @throws Throwable en cas d'erreur de serialization
     */
    public function getSavable():string{
        return @serialize(value: $this);
    }

    /**
     * @brief Vérifie si la procédure peut avancer
     * @param mixed ...$args arguments à envoyer à l'étape actuelle
     * @return bool si l'étape actuelle est validée
     */
    public function canAccessNext(mixed ...$args):bool{
        $currentStep = $this->getCurrentStep();

        return $currentStep !== null && $currentStep->canAccessNext($this,...$args);
    }

    /**
     * @param mixed ...$args paramètres à envoyer à la dernière étape
     * @return bool si la procédure est terminée
     */
    public function isFinished(mixed ...$args):bool{
        return $this->currentStep === count(value: $this->getSteps()) && $this->canAccessNext(...$args);
    }

    /**
     * @brief Charge une instance serializé ou crée une nouvelle si null fourni
     * @attention méthode à utiliser sur la sous class procédure
     * @param string|null $from version serializé
     * @return $this l'instance générée
     */
    public static function loadFrom(?string $from):static{
        try{
            $unSerializedVer = @unserialize(data: $from);

            return $unSerializedVer instanceof static ? $unSerializedVer : new static();
        }
        catch(Throwable){
            return new static();
        }
    }
}