<?php

namespace SaboCore\Application\ApplicationLaunchProcedure;

use PhpAddons\ProcedureManager\Procedure;
use PhpAddons\ProcedureManager\ProcedureStep;

/**
 * @brief configurable launch procedure
 */
class ApplicationLaunchProcedure extends Procedure {
    /**
     * @param ProcedureStep[] $steps launch procedure steps
     */
    public function __construct(protected array $steps){
        parent::__construct();
    }

    public function getSteps(): array{
        return $this->steps;
    }
}