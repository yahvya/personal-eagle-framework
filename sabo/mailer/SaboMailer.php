<?php

namespace Sabo\Mailer;

use PHPMailer\PHPMailer\PHPMailer;
use Sabo\Config\SaboConfig;
use Sabo\Config\SaboConfigAttributes;
use Sabo\Controller\Controller\SaboController;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Exception;
use Sabo\Config\EnvConfig;

/**
 * gestionnaire d'envoi de mail
 */
class SaboMailer extends PHPMailer{

    /**
     * configuration du mailer
     */
    private array $config;

    /**
     * @param subject le sujet du mail
     * @param config configuration du mailer (clés SaboMailerConfig->value)
     */
    public function __construct(array $config){
        $this->config = $config;
    }

    /**
     * envoi un mail aux destinataires
     * @param subject le sujet du mail
     * @param recipients les destinataires du mail
     * @param altBody contenu alternatif sur html non affiché
     * @param templatePath chemin du template twig à partir du dossier des mails
     * @param datasForTemplate tableau de données pour le template twig
     * @return bool si l'envoi a réussi
     * @throws Exception en mode debug en cas d'échec d'envoi du mail ou destinataires incorrects
     */
    public function sendMailFromTemplate(array $recipients,string $subject,string $altBody,string $templatePath,array $datasForTemplate):bool{
        $this->reset();

        // vériifcation de l'existance des destinataires
        if(empty($recipients) ){
            if(SaboConfig::getBoolConfig(SaboConfigAttributes::DEBUG_MODE) )
                throw new Exception("Les destinataires d'un mail ne peuvent être vide");
            else    
                return false;
        }

        $directoryPath = ROOT . SaboConfig::getStrConfig(SaboConfigAttributes::MAIL_FOLDER_PATH);

        $loader = new FilesystemLoader($directoryPath);

        $twig = new Environment($loader,[
            "debug" => SaboConfig::getBoolConfig(SaboConfigAttributes::DEBUG_MODE)
        ]);

        // ajout des extension
        foreach(SaboController::$twigExtensions as $twigExtension) $twig->addExtension($twigExtension);

        // récupération du contenu du mail
        $htmlMail = $twig->render($templatePath,array_merge($datasForTemplate,EnvConfig::getViewEnv() ) );
        
        // ajout des destinataires
        foreach($recipients as $recipient){
            if(gettype($recipient) != "array") $recipient = [$recipient];

            $this->addAddress(...$recipient);
        }

        $this->isHTML(true);
        $this->Body = $htmlMail;
        $this->AltBody = $altBody;
        $this->Subject = $subject;
        $this->From = $this->config[SaboMailerConfig::FROM_EMAIL->value];
        $this->FromName = $this->config[SaboMailerConfig::FROM_NAME->value];

        try{
            return $this->send();
        }
        catch(Exception $e){
            if(SaboConfig::getBoolConfig(SaboConfigAttributes::DEBUG_MODE) ) throw $e;
        }

        return false;
    }

    /**
     * envoi un mail aux destinataires
     * @param recipients les destinataires
     * @param subject le sujet du mail
     * @param mailContent le contenu du mail
     * @return bool si le mail s'est bien envoyé
     * @throws Exception en mode debug en cas d'échec d'envoi du mail ou destinataires incorrects
     */
    public function sendBasicMail(array $recipients,string $subject,string $mailContent,):bool{
        $this->reset();

        // vérification des destinataires

        if(empty($recipients) ){
            if(SaboConfig::getBoolConfig(SaboConfigAttributes::DEBUG_MODE) )
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
            if(SaboConfig::getBoolConfig(SaboConfigAttributes::DEBUG_MODE) ) throw $e;
        }
        
        return false;
    }

    /**
     * réinitialise le mailer
     */
    private function reset():void{
        $this->isSMTP();
        $this->CharSet = "UTF-8";
        $this->Encoding = "base64";
        $this->SMTPAuth = true;
        $this->Host = EnvConfig::getConfigEnv()["mailer"]["host"];
        $this->Username = EnvConfig::getConfigEnv()["mailer"]["email"];
        $this->Password = EnvConfig::getConfigEnv()["mailer"]["password"];
        $this->SMTPSecure = "ssl";
        $this->Port = 465;
        $this->clearAddresses();
        $this->clearAttachments();
        $this->Subject = $this->AltBody = $this->Body = "";
        $this->isHTML(false);
    }
}