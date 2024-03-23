<?php

namespace SaboCore\Utils\Mailer;

use Override;
use SaboCore\Config\EnvConfig;
use SaboCore\Config\MailerConfig;
use SaboCore\Routing\Application\Application;
use SaboCore\Routing\Response\BladeResponse;

/**
 * @brief Fournisseur de mail template blade
 * @author yahaya bathily https://github.com/yahvya
 */
class BladeMailProvider extends MailerTemplateProvider{
    #[Override]
    public function buildContent(): string{
        $factory = BladeResponse::newFactory(viewsPath: [
            APP_CONFIG->getConfig(name: "ROOT") . Application::getEnvConfig()
                ->getConfig(name: EnvConfig::MAILER_CONFIG->value)
                ->getConfig(name: MailerConfig::MAIL_TEMPLATES_DIR_PATH->value)
        ]);

        return $factory->make(view: $this->templatePath,data: $this->templateDatas)->render();
    }
}