<?php

namespace Yahvya\EagleFramework\Utils\Mailer;

use Override;
use Yahvya\EagleFramework\Config\EnvConfig;
use Yahvya\EagleFramework\Config\MailerConfig;
use Yahvya\EagleFramework\Routing\Application\Application;
use Yahvya\EagleFramework\Routing\Response\BladeResponse;

/**
 * @brief Blade file based mail provider
 */
class BladeMailProvider extends MailerTemplateProvider
{
    #[Override]
    public function buildContent(): string
    {
        $factory = BladeResponse::newFactory(viewsPath: [
            APP_CONFIG->getConfig(name: "ROOT") . Application::getEnvConfig()
                ->getConfig(name: EnvConfig::MAILER_CONFIG->value)
                ->getConfig(name: MailerConfig::MAIL_TEMPLATES_DIR_PATH->value)
        ]);

        return $factory->make(view: $this->templatePath, data: $this->templateDatas)->render();
    }
}