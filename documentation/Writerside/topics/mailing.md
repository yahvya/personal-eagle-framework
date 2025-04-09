# Mailing

> Parmi les utilitaires, le framework embarque un utilitaire de mail crée autour de la class <code>PHPMailer</code>

La class <code>SaboCore\Utils\Mailer\SaboMailer></code> permet l'envoi de mail via templates (blade, twig) en plus des fonctionnalités natives offertes par <code>PHPMailer</code>

## Configuration du mailer

> La configuration de mail est désactivée par défaut [dans l'environnement](configuration_elements.md#env-php). Il faut dé commenter les lignes et remplir les configurations requises.

## Envoi de mail classique
    
> La fonction <code>sendBasicMail</code> permet l'envoi d'un mail classique, il prend le sujet, le contenu textuel ainsi que les destinataires du mail.

<code-block lang="php">
$mailer = new SaboMailer();

$sendSuccess = $mailer->sendBasicMail(subject: "Sabo framework",mailContent: "Mail content",recipients: ["sabo.framework@github.com"]);
</code-block>

## Envoi de mail via template

> La fonction <code>sendMailFromTemplate</code> permet l'envoi de mail via template, il prend le sujet, les destinataires ainsi que le gestionnaire de template

**Focus sur le gestionnaire de template**

Les gestionnaires de template extends de la class <code>SaboCore\Utils\Mailer\MailerTemplateProvider</code>

Le framework en fourni deux par défaut permettant de rendre respectivement des mails à partir de templates <code>twig</code> ou <code>blade</code> ayant comme racine <code>src/views/mails</code>

<code>SaboCore\Utils\Mailer\BladeMailProvider</code> - <code>SaboCore\Utils\Mailer\TwigMailProvider</code>

<code-block lang="php">
$mailer = new SaboMailer();
$sendSuccess = $mailer->sendMailFromTemplate(
    subject: "Sabo framework",
    recipients: ["sabo.framework@github.com"],
    templateProvider: new BladeMailProvider(
        templatePath: "mail", # src/views/mail/mail.blade.php
        altContent: "Contenu alternatif",
        templateDatas: []
    )   
);
$sendSuccess = $mailer->sendMailFromTemplate(
    subject: "Sabo framework",
    recipients: ["sabo.framework@github.com"],
    templateProvider: new TwigMailProvider(
        templatePath: "mail.twig", # src/views/mail/mail.twig
        altContent: "Contenu alternatif",
        templateDatas: []
    )   
);
</code-block>


