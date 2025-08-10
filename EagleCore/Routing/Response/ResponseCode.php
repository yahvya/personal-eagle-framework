<?php

namespace Yahvya\EagleFramework\Routing\Response;

/**
 * @brief HTTP return codes
 */
enum ResponseCode: int
{
    /**
     * @brief The request was successful.
     */
    case OK = 200;

    /**
     * @brief The request was successfully processed and resulted in the creation of a resource.
     */
    case CREATED = 201;

    /**
     * @brief The response is empty (no content to return).
     */
    case NO_CONTENT = 204;

    /**
     * @brief The syntax of the request is incorrect.
     */
    case BAD_REQUEST = 400;

    /**
     * @brief Access to the resource is denied due to invalid credentials.
     */
    case UNAUTHORIZED = 401;

    /**
     * @brief The server understood the request but refused to process it.
     */
    case FORBIDDEN = 403;

    /**
     * @brief The requested resource was not found on the server.
     */
    case NOT_FOUND = 404;

    /**
     * @brief Internal server error.
     */
    case INTERNAL_SERVER_ERROR = 500;
}
