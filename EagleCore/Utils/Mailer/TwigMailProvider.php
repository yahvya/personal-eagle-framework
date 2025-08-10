<?php

namespace Yahvya\EagleFramework\Utils\Mailer;

use Override;
use Yahvya\EagleFramework\Config\EnvConfig;
use Yahvya\EagleFramework\Config\MailerConfig;
use Yahvya\EagleFramework\Routing\Application\Application;
use Yahvya\EagleFramework\Routing\Response\TwigResponse;

/**
 * @brief Twig file based mail provider
 */
class TwigMailProvider extends MailerTemplateProvider
{
    #[Override]
    public function buildContent(): string
    {
        $environment = TwigResponse::newEnvironment([
            APP_CONFIG->getConfig(name: "ROOT") . Application::getEnvConfig()
                ->getConfig(name: EnvConfig::MAILER_CONFIG->value)
                ->getConfig(name: MailerConfig::MAIL_TEMPLATES_DIR_PATH->value)
        ]);

        return $environment->render(name: $this->templatePath, context: $this->templateDatas);
    }
}