<?php

namespace Yahvya\EagleFramework\Utils\TwigExtensions;

use Yahvya\EagleFramework\Utils\Csrf\CsrfManager;

/**
 * Trait, which provide the 'generateCsrf' 'checkCsrf' methods
 */
trait TwigCsrfExtensionImpl
{
    /**
     * @brief Check if the token is valid, then delete it
     * @param string $token Provided csrf token
     * @return bool If the token is valid
     */
    public function checkCsrf(string $token): bool
    {
        return checkCsrf(token: $token);
    }

    /**
     * @brief Generate a csrf token
     * @return CsrfManager The token manager
     */
    public function generateCsrf(): CsrfManager
    {
        return generateCsrf();
    }
}