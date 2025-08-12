<?php

namespace Yahvya\EagleFramework\Utils\Api;

/**
 * @brief Configuration of request parameters
 */
enum EagleApiRequest
{
    // Method of converting data

    /**
     * @brief JSON conversion
     */
    case JSON_BODY;

    /**
     * @brief Use of http_build_query
     */
    case HTTP_BUILD_QUERY;

    /**
     * @brief No data contained
     */
    case NO_DATA;

    // Mode of retrieving the result of a request

    /**
     * @brief Keep the result as a string
     */
    case RESULT_AS_STRING;

    /**
     * @brief Convert a result in JSON string format into an array
     */
    case RESULT_AS_JSON_ARRAY;
}
