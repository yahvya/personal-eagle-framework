<?php

namespace SaboCore\Cli\Theme;

/**
 * @brief Configuration de thème d'affichage du cli
 * @author yahaya bathily https://github.com/yahvya/
 */
enum Theme:string{
    /**
     * @brief Style de texte basique
     */
    case BASIC_TEXT_STYLE = "basicText";

    /**
     * @brief Style de texte spécial
     */
    case SPECIAL_TEXT_STYLE = "specialText";

    /**
     * @brief Style du texte non important
     */
    case NOT_IMPORTANT_STYLE = "notImportantText";

    /**
     * @brief Style du texte titre important
     */
    case TITLE_STYLE = "titleStyle";

    /**
     * @brief Style du texte mis en avant
     */
    case HOVER_STYLE = "hoverStyle";

    /**
     * @brief erreur basique
     */
    case BASIC_ERROR_STYLE = "basicError";

    /**
     * @brief Erreur importante
     */
    case IMPORTANT_ERROR_STYLE = "importantError";
}