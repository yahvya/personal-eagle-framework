<?php

namespace Yahvya\EagleFramework\Database\System;

/**
 * @brief Database actions
 */
enum DatabaseActions: int
{
    /**
     * @brief Row insertion action
     */
    case MODEL_CREATE = 1;

    /**
     * @brief Action before row insertion
     */
    case BEFORE_MODEL_CREATE = 2;

    /**
     * @brief Action after row insertion
     */
    case AFTER_MODEL_CREATE = 3;

    /**
     * @brief Row update action
     */
    case MODEL_UPDATE = 4;

    /**
     * @brief Action before row update
     */
    case BEFORE_MODEL_UPDATE = 5;

    /**
     * @brief Action after row update
     */
    case AFTER_MODEL_UPDATE = 6;

    /**
     * @brief Row deletion action
     */
    case MODEL_DELETE = 7;

    /**
     * @brief Action before row deletion
     */
    case BEFORE_MODEL_DELETE = 8;

    /**
     * @brief Action after row deletion
     */
    case AFTER_MODEL_DELETE = 9;

    /**
     * @brief Action during the build cycle of the model php instance
     */
    case ON_GENERATION = 10;

    /**
     * @brief Action after the build of the model php instance
     */
    case AFTER_GENERATION = 11;

    /**
     * @brief Action before the build of the model php instance
     */
    case BEFORE_GENERATION = 12;
}
