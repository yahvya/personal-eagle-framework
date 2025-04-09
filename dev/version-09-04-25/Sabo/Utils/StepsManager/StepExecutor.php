<?php

namespace Sabo\Utils\StepsManager;

use Throwable;
use TypeError;

/**
 * Step executor manager
 */
class StepExecutor
{
    /**
     * @var Step|null Store the failed step during execution
     */
    public ?Step $lastFailedStep = null;

    /**
     * @var Step[] Steps
     */
    public readonly array $steps;

    /**
     * @param Step ...$steps Steps to execute in order
     */
    public function __construct(Step... $steps)
    {
        $this->steps = $steps;
    }

    /**
     * Execute the steps in the provided range
     * @param int $fromIndex Start array index (included)
     * @param int $toIndex End array index (included)
     * @param StepExecutionContext|null $executionContext Execution context or the empty context will be provided
     * @return bool If all steps execution succeed
     * @attention Update the lastFailedStep on failure
     * @attention Execute the same step in loop until canGoToNextStep return true
     */
    public function executeSteps(int $fromIndex,int $toIndex,?StepExecutionContext $executionContext = null):bool
    {
        # reset last failed step
        $this->lastFailedStep = null;

        # define a default context if nothing provided
        if($executionContext === null)
            $executionContext = new EmptyStepExecutionContext();

        try
        {
            while($fromIndex <= $toIndex)
            {
                // check if the index exists
                if(!array_key_exists(key: $fromIndex,array: $this->steps))
                    continue;

                if(!$this->steps[$fromIndex]->execute(executionContext: $executionContext))
                {
                    $this->lastFailedStep = $this->steps[$fromIndex];
                    return false;
                }

                if($this->steps[$fromIndex]->canGoToNextStep())
                    $fromIndex++;
            }

            return true;
        }
        catch (Throwable|TypeError)
        {
            return false;
        }
    }

    /**
     * Execute all steps
     * @param StepExecutionContext|null $executionContext Execution context or the empty context will be provided
     * @return bool If all steps execution succeed
     * @attention Update the lastFailedStep on failure
     * @attention Execute the same step in loop until canGoToNextStep return true
     */
    public function executeAll(?StepExecutionContext $executionContext = null):bool
    {
        return $this->executeSteps(fromIndex: 0,toIndex: count(value: $this->steps) - 1,executionContext: $executionContext);
    }
}