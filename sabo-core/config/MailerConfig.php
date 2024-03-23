<?php

namespace SaboCore\Config;

/**
 * @brief Configuration de mail
 * @author yahaya bathily https://github.com/yahvya
 */
enum MailerConfig:string{
    /**
     * @brief Email de l'envoyeur
     * @type string
     */
    case FROM_EMAIL = "fromEmail";

    /**
     * @brief Nom de l'envoyeur
     * @type string
     */
    case FROM_NAME = "fromName";

    /**
     * @brief Hôte fournisseur
     * @type string
     */
    case MAILER_PROVIDER_HOST = "mailerProviderHost";

    /**
     * @brief Nom d'utilisateur du fournisseur
     * @type string
     */
    case MAILER_PROVIDER_USERNAME = "mailerProviderUsername";

    /**
     * @brief Mot de passe du fournisseur
     * @type string
     */
    case MAILER_PROVIDER_PASSWORD = "mailerProviderPassword";

    /**
     * @brief Chemin de du dossier racine des templates de mails
     * @type string
     */
    case MAIL_TEMPLATES_DIR_PATH = "mailTemplatesDirPath";

    /**
     * @brief Port du fournisseur
     * @type int
     */
    case PROVIDER_PORT = "mailerProviderPort";
}
