<?php

namespace SaboCore\Utils\Mailer;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use SaboCore\Config\EnvConfig;
use SaboCore\Config\MailerConfig;
use SaboCore\Routing\Application\Application;
use Throwable;

/**
 * @brief Helper d'envoi de mail simplifié
 * @author yahaya bathily https://github.com/yahvya
 */
class SaboMailer extends PHPMailer {
    /**
     * @brief Envoi un mail aux destinataires
     * @param string $subject le sujet du mail
     * @param string[] $recipients les destinataires du mail
     * @param MailerTemplateProvider $templateProvider fournisseur de template
     * @return bool si l'envoi a réussi
     * @throws Throwable en mode debug en cas d'échec d'envoi du mail ou destinataires incorrects
     */
    public function sendMailFromTemplate(string $subject,array $recipients,MailerTemplateProvider $templateProvider):bool{
        $this->reset();

        try{
            $isDebugMode = Application::getEnvConfig()->getConfig(name: EnvConfig::DEV_MODE_CONFIG->value);

            // vérification de l'existence des destinataires
            if(empty($recipients) ){
                if($isDebugMode)
                    throw new Exception(message: "Les destinataires d'un mail ne peuvent être vide");
                else
                    return false;
            }

            // ajout des destinataires
            foreach($recipients as $recipient){
                if(gettype(value: $recipient) != "array") $recipient = [$recipient];

                $this->addAddress(...$recipient);
            }

            $this->isHTML();
            $this->Body = $templateProvider->buildContent();
            $this->AltBody = $templateProvider->getAltContent();
            $this->Subject = $subject;

            try{
                return $this->send();
            }
            catch(Exception $e){
                if($isDebugMode) throw $e;
            }
        }
        catch(Throwable){}

        return false;
    }

    /**
     * @brief Envoi un mail aux destinataires
     * @param string $subject le sujet du mail
     * @param string $mailContent le contenu du mail
     * @param string[] $recipients les destinataires
     * @return bool si le mail s'est bien envoyé
     * @throws Throwable en mode debug en cas d'échec d'envoi du mail ou destinataires incorrects
     */
    public function sendBasicMail(string $subject,string $mailContent,array $recipients):bool{
        $this->reset();

        try{
            $isDebugMode = Application::getEnvConfig()->getConfig(name: EnvConfig::DEV_MODE_CONFIG->value);

            // vérification des destinataires

            if(empty($recipients) ){
                if($isDebugMode)
                    throw new Exception("Les destinataires d'un mail ne peuvent être vide");
                else
                    return false;
            }

            // ajout des destinataires
            foreach($recipients as $recipient) $this->addAddress($recipient);

            $this->isHTML(false);
            $this->Subject = $subject;
            $this->Body = $mailContent;
            $this->AltBody = $mailContent;

            try{
                return $this->send();
            }
            catch(Exception $e){
                if($isDebugMode) throw $e;
            }
        }
        catch(Throwable){}

        return false;
    }

    /**
     * @brief réinitialise le mailer
     * @return $this
     * @throws Throwable en cas d'erreur
     */
    public function reset():SaboMailer{
        $config = Application::getEnvConfig()->getConfig(name: EnvConfig::MAILER_CONFIG->value);

        $config->checkConfigs(...array_map(fn(MailerConfig $case):string => $case->value,MailerConfig::cases()));

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