<?php

namespace Yahvya\EagleFramework\Utils\Mailer;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Yahvya\EagleFramework\Config\EnvConfig;
use Yahvya\EagleFramework\Config\MailerConfig;
use Yahvya\EagleFramework\Routing\Application\Application;
use Throwable;

/**
 * @brief Simplified mail sending helper
 */
class EagleMailer extends PHPMailer
{
    /**
     * @brief Sends an email to the recipients
     * @param string $subject the email subject
     * @param string[] $recipients the recipients of the email
     * @param MailerTemplateProvider $templateProvider template provider
     * @return bool whether the sending succeeded
     * @throws Throwable in debug mode in case of sending failure or invalid recipients
     */
    public function sendMailFromTemplate(string $subject, array $recipients, MailerTemplateProvider $templateProvider): bool
    {
        $this->reset();

        try
        {
            $isDebugMode = Application::getEnvConfig()->getConfig(name: EnvConfig::DEV_MODE_CONFIG->value);

            // check if recipients exist
            if (empty($recipients))
            {
                if ($isDebugMode)
                    throw new Exception(message: "Email recipients cannot be empty");
                else
                    return false;
            }

            // add recipients
            foreach ($recipients as $recipient)
            {
                if (gettype(value: $recipient) != "array") $recipient = [$recipient];

                $this->addAddress(...$recipient);
            }

            $this->isHTML();
            $this->Body = $templateProvider->buildContent();
            $this->AltBody = $templateProvider->altContent;
            $this->Subject = $subject;

            try
            {
                return $this->send();
            }
            catch (Exception $e)
            {
                if ($isDebugMode) throw $e;
            }
        }
        catch (Throwable)
        {
        }

        return false;
    }

    /**
     * @brief Sends an email to the recipients
     * @param string $subject the email subject
     * @param string $mailContent the email content
     * @param string[] $recipients the recipients
     * @return bool whether the email was sent successfully
     * @throws Throwable in debug mode in case of sending failure or invalid recipients
     */
    public function sendBasicMail(string $subject, string $mailContent, array $recipients): bool
    {
        $this->reset();

        try
        {
            $isDebugMode = Application::getEnvConfig()->getConfig(name: EnvConfig::DEV_MODE_CONFIG->value);

            // check recipients

            if (empty($recipients))
            {
                if ($isDebugMode)
                    throw new Exception("Email recipients cannot be empty");
                else
                    return false;
            }

            // add recipients
            foreach ($recipients as $recipient) $this->addAddress($recipient);

            $this->isHTML(false);
            $this->Subject = $subject;
            $this->Body = $mailContent;
            $this->AltBody = $mailContent;

            try
            {
                return $this->send();
            }
            catch (Exception $e)
            {
                if ($isDebugMode) throw $e;
            }
        }
        catch (Throwable)
        {
        }

        return false;
    }

    /**
     * @brief Resets the mailer
     * @return $this
     * @throws Throwable in case of error
     */
    public function reset(): EagleMailer
    {
        $config = Application::getEnvConfig()->getConfig(name: EnvConfig::MAILER_CONFIG->value);

        $config->checkConfigs(...array_map(fn(MailerConfig $case): string => $case->value, MailerConfig::cases()));

        $this->isSMTP();
        $this->CharSet = "UTF-8";
        $this->Encoding = "base64";
        $this->SMTPAuth = true;
        $this->Host = $config->getConfig(name: MailerConfig::MAILER_PROVIDER_HOST->value);
        $this->Username = $config->getConfig(name: MailerConfig::MAILER_PROVIDER_USERNAME->value);
        $this->Password = $config->getConfig(name: MailerConfig::MAILER_PROVIDER_PASSWORD->value);
        $this->From = $config->getConfig(name: MailerConfig::FROM_EMAIL->value);
        $this->FromName = $config->getConfig(name: MailerConfig::FROM_NAME->value);
        $this->SMTPSecure = "ssl";
        $this->Port = $config->getConfig(name: MailerConfig::PROVIDER_PORT->value);
        $this->clearAddresses();
        $this->clearAttachments();
        $this->Subject = $this->AltBody = $this->Body = "";
        $this->isHTML(isHtml: false);

        return $this;
    }
}
