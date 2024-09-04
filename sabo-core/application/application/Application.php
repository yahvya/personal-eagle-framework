<?php

namespace SaboCore\Application\Application;

/**
 * @brief application cycle manager
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
     * @brief load requirements by excluding routing step
     * @return $this
     */
    public function launch():self{
        return $this;
    }
}