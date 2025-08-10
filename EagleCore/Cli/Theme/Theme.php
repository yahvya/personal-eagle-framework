<?php

namespace Yahvya\EagleFramework\Cli\Theme;

/**
 * @brief CLI print configuration theme
 */
enum Theme: string
{
    /**
     * @brief Basic text style
     */
    case BASIC_TEXT_STYLE = "basicText";

    /**
     * @brief Special text style
     */
    case SPECIAL_TEXT_STYLE = "specialText";

    /**
     * @brief Basic text style
     */
    case NOT_IMPORTANT_STYLE = "notImportantText";

    /**
     * @brief Title style
     */
    case TITLE_STYLE = "titleStyle";

    /**
     * @brief Hover style
     */
    case HOVER_STYLE = "hoverStyle";

    /**
     * @brief Classic error style
     */
    case BASIC_ERROR_STYLE = "basicError";

    /**
     * @brief Important error style
     */
    case IMPORTANT_ERROR_STYLE = "importantError";
}