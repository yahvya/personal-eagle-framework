<?php

namespace Sabo\Utils\StepsManager;

/**
 * Step.
 * @attention A step class must not throw an exception
 */
abstract class Step
{
    /**
     * @var bool Define if the state have already been executed
     */
    protected bool $haveBeenExecuted = false;

    /**
     * @var bool Define if the last execution was a success. Can be modified outside the class
     */
    public bool $isLastExecutionSuccessful = false;

    /**
     * @var string|null Failure message if the step execution's fail
     */
    public ?string $failureMessage = null;

    /**
     * Execute the step
     * @param StepExecutionContext $executionContext Step execution's context
     * @return bool Execution success state
     */
    public abstract function execute(StepExecutionContext& $executionContext):bool;

    /**
     * @return bool The steps allow to continue the execution
     */
    public abstract function canGoToNextStep():bool;
}