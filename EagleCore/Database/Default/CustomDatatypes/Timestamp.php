<?php

namespace Yahvya\EagleFramework\Database\Default\CustomDatatypes;

use DateTime;

/**
 * @brief Custom model class type for timestamps
 */
class Timestamp
{
    /**
     * @var DateTime Date internal manager
     */
    protected DateTime $dateManager;

    /**
     * @param int|null $timestamp Timestamp to treat. If null "now" will be used
     */
    public function __construct(?int $timestamp = null)
    {
        $this->dateManager = new DateTime();
        $this->dateManager->setTimestamp($timestamp ?? time());
    }

    /**
     * @return int The ready value to be inserted in the database
     */
    public function convertForDatabase(): int
    {
        return $this->dateManager->getTimestamp();
    }

    /**
     * @return int The stored timestamp
     */
    public function getTimestamp(): int
    {
        return $this->dateManager->getTimestamp();
    }

    /**
     * @return DateTime Datetime instance based on the timestamp
     */
    public function toDateTime(): DateTime
    {
        return clone $this->dateManager;
    }

    /**
     * @brief Format the timestamp
     * @param string $format Format
     * @return string Formated value
     */
    public function format(string $format = "Y-m-d H:i:s"): string
    {
        return $this->dateManager->format(format: $format);
    }

    /**
     * @brief Convert a database column value into an instance
     * @param string $data Db timestamp stored
     * @return Timestamp Generate"d instance
     */
    public static function fromDatabase(mixed $data): Timestamp
    {
        return new Timestamp(timestamp: intval(value: $data));
    }
}