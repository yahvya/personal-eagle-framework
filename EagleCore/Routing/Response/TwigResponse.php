<?php

namespace Yahvya\EagleFramework\Routing\Response;

use ReflectionClass;
use Yahvya\EagleFramework\Config\EnvConfig;
use Yahvya\EagleFramework\Routing\Application\Application;
use Throwable;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * @brief Twig response
 */
class TwigResponse extends HtmlResponse
{
    /**
     * @param string $pathFromViews Path from the views directory
     * @param array $datas View data
     */
    public function __construct(string $pathFromViews, array $datas = [])
    {
        try
        {
            $environment = self::newEnvironment(viewsPath: [APP_CONFIG->getConfig(name: "ROOT") . "/Src/views/"]);

            parent::__construct(content: $environment->render(name: $pathFromViews, context: $datas));
        }
        catch (Throwable)
        {
            parent::__construct(content: "Veuillez rechargez la page");
        }
    }

    /**
     * @brief Create a twig environment
     * @param array $viewsPath View entry path
     * @return Environment|null Created environment or null
     */
    public static function newEnvironment(array $viewsPath): Environment|null
    {
        try
        {
            $loader = new FilesystemLoader(paths: $viewsPath);
            $environment = new Environment(
                loader: $loader,
                options: [
                    "cache" => APP_CONFIG->getConfig(name: "ROOT") . "/EagleCore/views/twig",
                    "debug" => Application::getEnvConfig()->getConfig(name: EnvConfig::DEV_MODE_CONFIG->value),

                ]);

            $extensions = registerTwigExtensions();

            foreach ($extensions as $extension)
                $environment->addExtension(extension: new ReflectionClass(objectOrClass: $extension)->newInstance());

            return $environment;
        }
        catch (Throwable)
        {
            return null;
        }
    }
}