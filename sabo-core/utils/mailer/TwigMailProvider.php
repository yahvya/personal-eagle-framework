<?php

namespace SaboCore\Utils\Mailer;

use Override;
use SaboCore\Config\EnvConfig;
use SaboCore\Config\MailerConfig;
use SaboCore\Routing\Application\Application;
use SaboCore\Routing\Response\TwigResponse;

/**
 * @brief Fournisseur de mail template twig
 * @author yahaya bathily https://github.com/yahvya
 */
class TwigMailProvider extends MailerTemplateProvider{
    #[Override]
    public function buildContent(): string{
        $environment = TwigResponse::newEnvironment([
            APP_CONFIG->getConfig(name: "ROOT") . Application::getEnvConfig()
                ->getConfig(name: EnvConfig::MAILER_CONFIG->value)
                ->getConfig(name: MailerConfig::MAIL_TEMPLATES_DIR_PATH->value)
        ]);

        return $environment->render(name: $this->templatePath,context: $this->templateDatas);
    }
}