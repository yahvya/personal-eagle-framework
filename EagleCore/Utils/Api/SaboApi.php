<?php

namespace Yahvya\EagleFramework\Utils\Api;

use Exception;
use ReflectionClass;

/**
 * @brief API curl request utility
 */
abstract class SaboApi
{
    /**
     * @var string API URL prefix
     */
    protected string $apiUrlPrefix;

    /**
     * @var array Stored request results
     */
    protected array $storedRequestResult;

    /**
     * @var string|null Value of the last executed request, null if no result
     */
    private ?string $lastRequestResult;

    /**
     * @param string $apiUrlPrefix URL prefix for API calls
     */
    public function __construct(string $apiUrlPrefix)
    {
        $this->apiUrlPrefix = !str_ends_with(haystack: $apiUrlPrefix, needle: "/") && !str_ends_with(haystack: $apiUrlPrefix, needle: "\\") ? $apiUrlPrefix . "/" : $apiUrlPrefix;
        $this->lastRequestResult = null;
        $this->storedRequestResult = [];
    }

    /**
     * @brief Provides a URL based on the API prefix
     * @param string $apiSuffix Suffix to append
     * @return string The URL composed of the API prefix and the suffix
     */
    protected function apiUrl(string $apiSuffix): string
    {
        return $this->apiUrlPrefix . $apiSuffix;
    }

    /**
     * @brief Performs a curl request from the given configuration and updates lastRequestResult if successful
     * @param string $requestUrl Request URL (based on apiUrl function)
     * @param array $headers Request headers
     * @param mixed $data Request data
     * @param SaboApiRequest $dataConversionType Data conversion type, default json_encode [JSON_BODY|HTTP_BUILD_QUERY|NO_DATA]; NO_DATA if no data should be sent
     * @param array $overrideCurlOptions Array overriding default curl options, indexed by curl constants
     * @param string|null $storeIn If not null, stores the request result in "storedRequestResult" with the given key
     * @return bool True if the request succeeded
     */
    protected function request(string $requestUrl, array $headers, mixed $data, SaboApiRequest $dataConversionType, array $overrideCurlOptions = [], ?string $storeIn = null): bool
    {
        $curl = curl_init();

        if ($curl === false) return false;

        // Default options
        $options = [
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true
        ];

        // Override options
        foreach ($overrideCurlOptions as $curlOption => $value) $options[$curlOption] = $value;

        $options[CURLOPT_HTTPHEADER] = $headers;
        $options[CURLOPT_URL] = $requestUrl;

        if (SaboApiRequest::NO_DATA != $dataConversionType)
            $options[CURLOPT_POSTFIELDS] = $dataConversionType == SaboApiRequest::HTTP_BUILD_QUERY ? http_build_query(data: $data) : @json_encode(value: $data);

        if (!curl_setopt_array(handle: $curl, options: $options)) return false;

        $result = curl_exec(handle: $curl);

        if ($storeIn !== null) $this->storedRequestResult[$storeIn] = $result;

        if ($options[CURLOPT_RETURNTRANSFER])
        {
            if ($result === false) return false;

            $this->lastRequestResult = $result;

            return true;
        }

        return $result;
    }

    /**
     * @param SaboApiRequest $as Defines how the data should be returned [RESULT_AS_JSON_ARRAY|RESULT_AS_STRING]
     * @return string|array|null The data from the last request, or null
     */
    protected function getLastRequestResult(SaboApiRequest $as): string|array|null
    {
        if ($this->lastRequestResult == null) return null;

        switch ($as)
        {
            case SaboApiRequest::RESULT_AS_JSON_ARRAY :
                $jsonData = @json_decode(json: $this->lastRequestResult, associative: true);

                return gettype(value: $jsonData) != "array" ? null : $jsonData;

            case SaboApiRequest::RESULT_AS_STRING:
                return $this->lastRequestResult;

            default:
                return null;
        }
    }

    /**
     * @brief Checks if the given array contains the specified keys
     * @param array $toCheck Data array
     * @param string ...$keysToCheck Keys to check, format "level1.level2" for an array ["level1" → ["level2" → 2]]
     * @return bool True if the keys exist in the array
     */
    protected static function ifArrayContain(array $toCheck, string ...$keysToCheck): bool
    {
        foreach ($keysToCheck as $keyToCheck)
        {
            $arrayCopy = $toCheck;

            $keys = explode(separator: ".", string: $keyToCheck);

            foreach ($keys as $key)
            {
                if (gettype(value: $arrayCopy) != "array" || !array_key_exists(key: $key, array: $arrayCopy)) return false;

                $arrayCopy = $arrayCopy[$key];
            }
        }

        return true;
    }

    /**
     * @brief Creates an object from the API configuration
     * @attention Should be called with the child class
     * @param array $config Array indexed by SaboApiConfig->value
     * @return mixed The created object or null
     */
    public static function createFromConfig(array $config): mixed
    {
        try
        {
            $reflection = new ReflectionClass(objectOrClass: get_called_class());

            return $reflection->newInstance(
                $config[SaboApiConfig::URL->value]
            );
        } catch (Exception)
        {
            return null;
        }
    }
}
