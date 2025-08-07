<?php

namespace SaboCore\Core\Global;

/**
 * Application configuration
 */
class ApplicationConfiguration
{
    /**
     * @var string Application name
     */
    protected string $appName;

    /**
     * @var string Application version
     */
    protected string $appVersion;

    /**
     * @var bool Application maintenance state
     */
    protected bool $isInMaintenance;

    /**
     * @var string Application timezone
     */
    protected string $timezone;

    /**
     * @return string Application timezone
     */
    public function getTimezone(): string
    {
        return $this->timezone;
    }

    /**
     * Set the application default timezone
     * @param string $timezone Timezone in PHP format
     * @param bool $persist If true set the default timezone of php at the same time
     * @return $this
     */
    public function setTimezone(string $timezone,bool $persist = true): static
    {
        $this->timezone = $timezone;

        if($persist)
            date_default_timezone_set(timezoneId: $timezone);

        return $this;
    }

    /**
     * @return string Application name
     */
    public function getAppName(): string
    {
        return $this->appName;
    }

    /**
     *
     * @param string $appName
     * @return $this
     */
    public function setAppName(string $appName): static
    {
        $this->appName = $appName;
        return $this;
    }

    /**
     * @return string Application version
     */
    public function getAppVersion(): string
    {
        return $this->appVersion;
    }

    /**
     * Set the application version
     * @param string $appVersion New application version
     * @return $this
     */
    public function setAppVersion(string $appVersion): static
    {
        $this->appVersion = $appVersion;
        return $this;
    }

    /**
     * @return bool Maintenance state
     */
    public function isInMaintenance(): bool
    {
        return $this->isInMaintenance;
    }

    /**
     * Set application maintenance state
     * @param bool $isInMaintenance New maintenance state
     * @return $this
     */
    public function setIsInMaintenance(bool $isInMaintenance): static
    {
        $this->isInMaintenance = $isInMaintenance;
        return $this;
    }
}