<?php

namespace SaboCore\Routing\Response;

/**
 * @brief Http response codes
 */
enum ResponseCode:int{
    /**
     * @brief Successful request
     */
    case OK = 200;

    /**
     * @brief Request treated with success and create a resource
     */
    case CREATED = 201;

    /**
     * @brief Empty response
     */
    case NO_CONTENT = 204;

    /**
     * @brief Bad request
     */
    case BAD_REQUEST = 400;

    /**
     * @brief unauthorized request
     */
    case UNAUTHORIZED = 401;

    /**
     * @brief server don't want to treat the request
     */
    case FORBIDDEN = 403;

    /**
     * @brief resource not found
     */
    case NOT_FOUND = 404;

    /**
     * @brief internal error
     */
    case INTERNAL_SERVER_ERROR = 500;
}