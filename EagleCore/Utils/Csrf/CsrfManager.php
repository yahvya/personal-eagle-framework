<?php

namespace Yahvya\EagleFramework\Utils\Csrf;

/**
 * @brief CSRF token manager
 */
class CsrfManager
{
    /**
     * @var string The CSRF token
     */
    protected(set) string $token;

    /**
     * @param string $token The CSRF token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * @return string The serialized version
     */
    public function serialize(): string
    {
        return serialize(value: $this);
    }

    /**
     * @param string $instance The serialized instance
     * @return CsrfManager The deserialized version
     */
    public static function deserialize(string $instance): CsrfManager
    {
        return unserialize(data: $instance);
    }
}
