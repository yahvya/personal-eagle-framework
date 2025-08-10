<?php

namespace Yahvya\EagleFramework\Routing\Request;

use Yahvya\EagleFramework\Treatment\TreatmentException;
use Yahvya\EagleFramework\Utils\FileManager\FormFileManager;
use Yahvya\EagleFramework\Utils\Session\SessionStorage;

/**
 * @brief Request data manager
 */
class Request
{
    /**
     * @var SessionStorage Session storage manager instance
     */
    protected(set) SessionStorage $sessionStorage;

    /**
     * @var array en-têtes de la requête
     */
    protected array $headers;

    public function __construct()
    {
        $this->sessionStorage = SessionStorage::create();
        $headers = apache_request_headers();
        $this->headers = $headers !== false ? $headers : [];
    }

    /**
     * @brief Find POST data
     * @param string|null $errorMessage If non-null, a displayable treatment exception will be thrown if any key isn't found
     * @param string ...$toGet Keys which associated values should be retrieved
     * @return array|null Founded values indexed by their keys or null
     * @throws TreatmentException In case of a not found key and non-null $errorMessage
     */
    public function getPostValues(?string $errorMessage = null, string ...$toGet): ?array
    {
        $values = self::getValuesFrom($_POST, ...$toGet);

        if ($values === null)
        {
            if ($errorMessage !== null)
                throw new TreatmentException(message: $errorMessage, isDisplayable: true);

            return null;
        }

        return $values;
    }

    /**
     * @brief Find GET data
     * @param string|null $errorMessage If non-null, a displayable treatment exception will be thrown if any key isn't found
     * @param string ...$toGet Keys which associated values should be retrieved
     * @return array|null Founded values indexed by their keys or null
     * @throws TreatmentException In case of a not found key and non-null $errorMessage
     */
    public function getGetValues(?string $errorMessage = null, string ...$toGet): ?array
    {
        $values = self::getValuesFrom($_GET, ...$toGet);

        if ($values === null)
        {
            if ($errorMessage !== null)
                throw new TreatmentException(message: $errorMessage, isDisplayable: true);

            return null;
        }

        return $values;
    }

    /**
     * @brief Find COOKIE data
     * @param string|null $errorMessage If non-null, a displayable treatment exception will be thrown if any key isn't found
     * @param string ...$toGet Keys which associated values should be retrieved
     * @return array|null Founded values indexed by their keys or null
     * @throws TreatmentException In case of a not found key and non-null $errorMessage
     */
    public function getCookieValues(?string $errorMessage = null, string ...$toGet): ?array
    {
        $values = self::getValuesFrom($_COOKIE, ...$toGet);

        if ($values === null)
        {
            if ($errorMessage !== null)
                throw new TreatmentException(message: $errorMessage, isDisplayable: true);

            return null;
        }

        return $values;
    }

    /**
     * @brief Find FILES data
     * @param string|null $errorMessage If non-null, a displayable treatment exception will be thrown if any key isn't found
     * @param string ...$toGet Keys which associated values should be retrieved
     * @return array{string:FormFileManager}|null Founded values associated with a linked form file manager or null
     * @throws TreatmentException In case of a not found key and non-null $errorMessage
     */
    public function getFilesValues(?string $errorMessage = null, string ...$toGet): ?array
    {
        $values = self::getValuesFrom($_FILES, ...$toGet);

        if ($values === null)
        {
            if ($errorMessage !== null)
                throw new TreatmentException(message: $errorMessage, isDisplayable: true);

            return null;
        }

        foreach ($values as $key => $file)
            $values[$key] = new FormFileManager(fileDatas: $file);

        return $values;
    }

    /**
     * @brief Provide a request header
     * @param string $header Header name
     * @return string|null Value or null when not found
     */
    public function getHeader(string $header): string|null
    {
        return $this->headers[$header] ?? null;
    }

    /**
     * @return string The lowercased request method
     */
    public function getMethod(): string
    {
        return strtolower(string: $_SERVER["REQUEST_METHOD"]);
    }

    /**
     * @brief Get values from a container
     * @param array $container The data container
     * @param string ...$toGet Keys to find
     * @return array|null Founded values or null
     */
    protected static function getValuesFrom(array $container, string ...$toGet): ?array
    {
        $result = [];

        foreach ($toGet as $key)
        {
            if (!array_key_exists(key: $key, array: $container))
                return null;

            $result[$key] = $container[$key];
        }

        return $result;
    }
}