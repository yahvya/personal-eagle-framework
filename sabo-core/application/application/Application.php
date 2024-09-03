<?php

namespace SaboCore\Application\Application;

/**
 * @brief Application cycle manager
 */
class Application{
    /**
     * @brief load requirements for web app
     * @return $this
     */
    public function launchWeb():self{
        return $this;
    }

    /**
     * @brief load requirements for cron tasks
     * @return $this
     */
    public function launchCron():self{
        return $this;
    }
}