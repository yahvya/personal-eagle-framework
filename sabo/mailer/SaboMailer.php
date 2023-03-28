<?php

namespace Sabo\Mailer;

use PHPMailer\PHPMailer\PHPMailer;
use Sabo\Config\SaboConfig;
use Sabo\Config\SaboConfigAttributes;
use Sabo\Controller\Controller\SaboController;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Exception;

/**
 * gestionnaire d'envoi de mail
 */
class SaboMailer extends PHPMailer{
    /**
     * objet du mail
     */
    private string $subject;

    /**
     * configuration du mailer
     */
    private array $config;

    /**
     * @param subject le sujet du mail
     * @param config configuration du mailer (clés SaboMailerConfig->value)
     */
    public function __construct(string $subject,array $config){
        $this->subject = $subject;
        $this->config = $config;
    }

    /**
     * @param recipients les destinataires du mail
     * @param altBody contenu alternatif sur html non affiché
     * @param templatePath chemin du template twig à partir du dossier des mails
     * @param datasForTemplate tableau de données pour le template twig
     * @return bool si l'envoi a réussi
     */
    public function sendMailFromTemplate(array $recipients,string $altBody,string $templatePath,array $datasForTemplate):bool{
        $this->reset();

        $directoryPath = ROOT . SaboConfig::getStrConfig(SaboConfigAttributes::MAIL_FOLDER_PATH);

        $loader = new FilesystemLoader($directoryPath);

        $twig = new Environment($loader,[
            "debug" => SaboConfig::getBoolConfig(SaboConfigAttributes::DEBUG_MODE)
        ]);

        // ajout des extension
        foreach(SaboController::$twigExtensions as $twigExtension) $twig->addExtension($twigExtension);

        // récupération du contenu du mail
        $htmlMail = $twig->render($templatePath,$datasForTemplate);
        
        // ajout des destinataires
        foreach($recipients as $recipient){
            if(gettype($recipient) != "array") $recipient = [$recipient];

            $this->addAddress(...$recipient);
        }

        $this->isHTML(true);
        $this->Body = $htmlMail;
        $this->AltBody = $altBody;
        $this->Subject = $this->subject;
        $this->From = $this->config[SaboMailerConfig::FROM_EMAIL->value];
        $this->FromName = $this->config[SaboMailerConfig::FROM_NAME->value];

        try{
            return $this->send();
        }
        catch(Exception){}

        return false;
    }

    /**
     * réinitialise le mailer
     */
    private function reset():void{
        $this->clearAddresses();
        $this->clearAttachments();
    }
}