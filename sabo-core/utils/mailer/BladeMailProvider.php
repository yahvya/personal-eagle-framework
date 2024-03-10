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
        $factory = BladeResponse::newFactory([
            APP_CONFIG->getConfig("ROOT") . Application::getEnvConfig()
                ->getConfig(EnvConfig::MAILER_CONFIG->value)
                ->getConfig(MailerConfig::MAIL_TEMPLATES_DIR_PATH->value)
        ]);

        return $factory->make($this->templatePath,$this->templateDatas)->render();
    }
}