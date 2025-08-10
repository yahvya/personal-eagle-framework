<?php

namespace Yahvya\EagleFramework\Config;

/**
 * @brief Mail configuration
 */
enum MailerConfig: string
{
    /**
     * @brief Receiver email
     * @type string
     */
    case FROM_EMAIL = "fromEmail";

    /**
     * @brief Sender email
     * @type string
     */
    case FROM_NAME = "fromName";

    /**
     * @brief Mailer host
     * @type string
     */
    case MAILER_PROVIDER_HOST = "mailerProviderHost";

    /**
     * @brief Mail provider name
     * @type string
     */
    case MAILER_PROVIDER_USERNAME = "mailerProviderUsername";

    /**
     * @brief Mail provider password
     * @type string
     */
    case MAILER_PROVIDER_PASSWORD = "mailerProviderPassword";

    /**
     * @brief Mail templates root path
     * @type string
     */
    case MAIL_TEMPLATES_DIR_PATH = "mailTemplatesDirPath";

    /**
     * @brief Mail provider port
     * @type int
     */
    case PROVIDER_PORT = "mailerProviderPort";
}
