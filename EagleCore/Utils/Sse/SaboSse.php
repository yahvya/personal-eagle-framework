<?php

namespace Yahvya\EagleFramework\Utils\Sse;

use Throwable;
use Yahvya\EagleFramework\Utils\Verification\Verifier;

/**
 * @brief Sse event utility
 */
class SaboSse
{
    /**
     * @param ResourceManager|null $resourceManager Sse resource manager
     * @param int $sleepTimeSec Wait time in seconds during the loop execution
     */
    public function __construct(
        protected  (set) ResourceManager|null $resourceManager = new ResourceManager(),
        public int $sleepTimeSec = 1
    )
    {
    }

    /**
     * @brief Launch the sse execution
     * @param Callable $executor Sse action handler, receive $this as the only argument
     * @param Verifier|null $stopVerifier Stop verifier to check if the sse loop should stop. If the returned value is true, the loop will be stopped. It receives $this as the only argument
     * @param string $stopEventName Name of the event the user receives when the loop is stopped
     * @return $this After the loop ends
     * @notice Redefine the setup method to apply some configurations before the loop start
     */
    public function launch(callable $executor, ?Verifier $stopVerifier = null, string $stopEventName = "close"): SaboSse
    {
        $this->setup();

        while (true)
        {

            call_user_func_array(callback: $executor, args: [$this]);

            if ($stopVerifier !== null && $stopVerifier->verify(verifierArgs: [$this]))
            {
                $this->sendEvent(eventName: $stopEventName, eventDatas: []);
                break;
            }

            if (connection_aborted())
                break;

            sleep(seconds: $this->sleepTimeSec);
        }

        return $this;
    }

    /**
     * @brief Send json formated event data
     * @param array $eventDatas Message content
     * @param Callable|null $onError Error handler when the event sending fails, receive $this as the only argument
     * @return $this
     */
    public function sendEvent(string $eventName, array $eventDatas, ?callable $onError = null): SaboSse
    {
        try
        {
            $encodedDatas = @json_encode(value: $eventDatas);

            if ($encodedDatas === false)
            {
                if ($onError !== null)
                    call_user_func_array(callback: $onError, args: [$this]);

                return $this;
            }

            echo "event: $eventName" . PHP_EOL;
            echo "data: $encodedDatas";
            echo PHP_EOL . PHP_EOL;
            ob_flush();
            flush();
        } catch (Throwable)
        {
            if ($onError !== null)
                call_user_func_array(callback: $onError, args: [$this]);
        }

        return $this;
    }

    /**
     * @brief Configure the sse
     * @return $this
     * @attention If you redefine this method, call the parent::setup() method at the top of your method
     */
    protected function setup(): SaboSse
    {
        session_write_close();
        ignore_user_abort(enable: true);
        header(header: "Content-Type: text/event-stream");
        header(header: "Cache-Control: no-cache");
        ob_flush();
        flush();

        return $this;
    }
}
