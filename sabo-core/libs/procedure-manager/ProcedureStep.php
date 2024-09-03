<?php

namespace PhpAddons\ProcedureManager;

/**
 * @brief Description d'une étape de procédure
 * @author yahaya https//github.com/yahvya
 */
interface ProcedureStep{
    /**
     * @param Procedure $procedure parent procedure
     * @param mixed ...$args arguments
     * @return bool Si l'étape est validée
     */
    public function canAccessNext(Procedure $procedure,mixed ...$args):bool;
}